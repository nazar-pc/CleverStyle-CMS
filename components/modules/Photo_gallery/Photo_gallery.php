<?php
/**
 * @package		Photo gallery
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */
namespace	cs\modules\Photo_gallery;
use			cs\Cache\Prefix,
			cs\Config,
			cs\Language,
			cs\Storage,
			cs\Text,
			cs\Trigger,
			cs\User,
			cs\DB\Accessor,
			cs\Singleton,
			cs\plugins\SimpleImage\SimpleImage,
			Exception;
/**
 * @method static \cs\modules\Photo_gallery\Photo_gallery instance($check = false)
 */
class Photo_gallery {
	use Accessor,
		Singleton;

	/**
	 * @var Prefix
	 */
	protected	$cache;

	protected function construct () {
		$this->cache	= new Prefix('Photo_gallery');
		$module_data	= Config::instance()->module('Photo_gallery');
		if (!$module_data->directory_created) {
			$this->storage()->mkdir('Photo_gallery');
			$module_data->directory_created	= 1;
		}
	}
	/**
	 * Returns database index
	 *
	 * @return int
	 */
	protected function cdb () {
		return Config::instance()->module('Photo_gallery')->db('galleries');
	}
	/**
	 * Storage instance
	 *
	 * @return \cs\False_class|Storage\_Abstract
	 */
	protected function storage () {
		return Storage::instance()->storage(
			Config::instance()->module('Photo_gallery')->storage('galleries')
		);
	}
	/**
	 * Get data of specified image
	 *
	 * @param int|int[]				$id
	 *
	 * @return array|array[]|bool
	 */
	function get ($id) {
		if (is_array($id)) {
			foreach ($id as &$i) {
				$i	= $this->get($i);
			}
			return $id;
		}
		$L			= Language::instance();
		$id			= (int)$id;
		return $this->cache->get("images/$id/$L->clang", function () use ($id) {
			if ($data = $this->db()->qf([
				"SELECT
					`id`,
					`gallery`,
					`user`,
					`date`,
					`title`,
					`description`,
					`original`,
					`preview`
				FROM `[prefix]photo_gallery_images`
				WHERE `id` = '%s'
				LIMIT 1",
				$id
			])) {
				$data['title']			= $this->ml_process($data['title']);
				$data['description']	= $this->ml_process($data['description']);
			}
			return $data;
		});
	}
	/**
	 * Add new image
	 *
	 * @param string	$original		Absolute path to original image
	 * @param string	$gallery		Gallery id
	 * @param string	$title
	 * @param string	$description
	 *
	 * @return bool|int					Id of created post on success of <b>false</> on failure
	 */
	function add ($original, $gallery, $title = '', $description = '') {
		if (empty($original) || !$gallery) {
			return false;
		}
		$gallery		= (int)$gallery;
		if ($this->db_prime()->q(
			"INSERT INTO `[prefix]photo_gallery_images`
				(
					`gallery`,
					`user`,
					`date`,
					`original`
				)
			VALUES
				(
					'%s',
					'%s',
					'%s',
					'%s'
				)",
			$gallery,
			User::instance()->id,
			TIME,
			$original
		)) {
			$id	= $this->db_prime()->id();
			if ($this->set($id, $title, $description)) {
				Trigger::instance()->run(
					'System/upload_files/add_tag',
					[
						'tag'	=> "Photo_gallery/images/$id",
						'url'	=> $original
					]
				);
				$tmp_file	= TEMP.'/'.User::instance()->id.'_'.md5(MICROTIME);
				try {
					$SimpleImage	= new SimpleImage($original);
					$SimpleImage->thumbnail(256)->save($tmp_file = "$tmp_file.".$SimpleImage->get_original_info()['format']);
					unset($SimpleImage);
				} catch (Exception $e) {
					$this->del($id);
					trigger_error($e->getMessage(), E_USER_WARNING);
					return false;
				}
				$storage	= $this->storage();
				if (!$storage->file_exists("Photo_gallery/$gallery")) {
					$storage->mkdir("Photo_gallery/$gallery");
				}
				$copy_result = $storage->copy($tmp_file, $new_file = "Photo_gallery/$gallery/{$id}_".md5(MICROTIME));
				unlink($tmp_file);
				if (!$copy_result) {
					$this->del($id);
					return false;
				}
				$this->db_prime()->q(
					"UPDATE `[prefix]photo_gallery_images`
					SET `preview` = '%s'
					WHERE `id` = '%s'",
					$storage->url_by_source($new_file),
					$id
				);
				unset(
					$new_file,
					$this->cache->{"galleries/$gallery"}
				);
				return $id;
			} else {
				$this->db_prime()->q(
					"DELETE FROM `[prefix]photo_gallery_images`
					WHERE `id` = $id
					LIMIT 1"
				);
			}
		}
		return false;
	}
	/**
	 * Set data of specified image
	 *
	 * @param int		$id
	 * @param string	$title
	 * @param string	$description
	 *
	 * @return bool
	 */
	function set ($id, $title, $description) {
		$User			= User::instance();
		$id				= (int)$id;
		$title			= xap(trim($title));
		$description	= xap(trim($description));
		if ($this->db_prime()->q(
			"UPDATE `[prefix]photo_gallery_images`
			SET
				`title`			= '%s',
				`description`	= '%s'
			WHERE
				`id` = '%s' AND
				(
					`user`	= '%s' OR
					%d
				)
			LIMIT 1",
			$title ? $this->ml_set('Photo_gallery/images/title', $id, $title) : '',
			$description ? $this->ml_set('Photo_gallery/images/description', $id, $description) : '',
			$id,
			$User->id,
			(int)$User->admin()
		)) {
			unset($this->cache->{"images/$id"});
			return true;
		}
		return false;
	}
	/**
	 * Delete specified image
	 *
	 * @param int	$id
	 *
	 * @return bool
	 */
	function del ($id) {
		$id		= (int)$id;
		$data	= $this->get($id);
		if ($this->db_prime()->q(
			"DELETE FROM `[prefix]photo_gallery_images`
			WHERE `id` = $id
			LIMIT 1"
		)) {
			$this->ml_del('Photo_gallery/images/title', $id);
			$this->ml_del('Photo_gallery/images/description', $id);
			Trigger::instance()->run(
				'System/upload_files/del_tag',
				[
					'tag'	=> "Photo_gallery/images/$id"
				]
			);
			$this->storage()->unlink($this->storage()->source_by_url($data['preview']));
			$Cache	= $this->cache;
			unset(
				$Cache->{"images/$id"},
				$Cache->{"galleries/$data[gallery]"}
			);
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Get array of galleries in form [<i>path</i> => <i>id</i>]
	 *
	 * @return array|bool
	 */
	function get_galleries_list () {
		$L		= Language::instance();
		return $this->cache->get("galleries/list/$L->clang", function () {
			$data	= [];
			foreach (
				$this->db()->qfas(
					"SELECT `id`
					FROM `[prefix]photo_gallery_galleries`
					WHERE `active` = 1
					ORDER BY `order` ASC"
				) as $gallery
			) {
				$data[$this->get_gallery($gallery)['path']]	= $gallery;
			}
			return $data;
		});
	}
	/**
	 * Get data of specified gallery
	 *
	 * @param int|int[]				$id
	 *
	 * @return array|array[]|bool
	 */
	function get_gallery ($id) {
		if (is_array($id)) {
			foreach ($id as &$i) {
				$i	= $this->get_gallery($i);
			}
			return $id;
		}
		$L		= Language::instance();
		$id		= (int)$id;
		return $this->cache->get("galleries/$id/$L->clang", function () use ($id) {
			if ($data = $this->db()->qf([
				"SELECT
					`id`,
					`title`,
					`path`,
					`description`,
					`active`,
					`preview_image`,
					(
						SELECT COUNT(`id`)
						FROM `[prefix]photo_gallery_images`
						WHERE `gallery` = '%1\$s'
					) AS `images`
				FROM `[prefix]photo_gallery_galleries`
				WHERE `id` = '%1\$s'
				LIMIT 1",
				$id
			])) {
				$data['title']			= $this->ml_process($data['title']);
				$data['path']			= $this->ml_process($data['path']);
				$data['description']	= $this->ml_process($data['description']);
				$order					= $data['preview_image'] == 'first' ? 'ASC' : 'DESC';
				$data['preview']		= $this->db()->qfs(
					"SELECT `preview`
					FROM `[prefix]photo_gallery_images`
					WHERE `gallery` = $data[id]
					ORDER BY `id` $order"
				) ?: '';
				$data['images']			= $this->db()->qfas(
					"SELECT `id`
					FROM `[prefix]photo_gallery_images`
					WHERE `gallery` = $data[id]
					ORDER BY `id` $order"
				) ?: [];
			}
			return $data;
		});
	}
	/**
	 * Add new gallery
	 *
	 * @param string	$title
	 * @param string	$path
	 * @param string	$description
	 * @param int		$active
	 * @param string	$preview_image
	 *
	 * @return bool|int					Id of created gallery on success of <b>false</> on failure
	 */
	function add_gallery ($title, $path, $description, $active, $preview_image) {
		if ($this->db_prime()->q(
			"INSERT INTO `[prefix]photo_gallery_galleries`
				(`active`)
			VALUES
				('%s')",
			(int)(bool)$active
		)) {
			$id		= $this->db_prime()->id();
			$this->set_gallery($id, $title, $path, $description, $active, $preview_image);
			return $id;
		} else {
			return false;
		}
	}
	/**
	 * Set data of specified gallery
	 *
	 * @param int		$id
	 * @param string	$title
	 * @param string	$path
	 * @param string	$description
	 * @param int		$active
	 * @param string	$preview_image
	 *
	 * @return bool
	 */
	function set_gallery ($id, $title, $path, $description, $active, $preview_image) {
		$path			= path($path ?: $title);
		$title			= xap(trim($title));
		$description	= xap(trim($description));
		$id				= (int)$id;
		if ($this->db_prime()->q(
			"UPDATE `[prefix]photo_gallery_galleries`
			SET
				`title`			= '%s',
				`path`			= '%s',
				`description`	= '%s',
				`active`		= '%s',
				`preview_image`	= '%s'
			WHERE `id` = '%s'
			LIMIT 1",
			$this->ml_set('Photo_gallery/galleries/title', $id, $title),
			$this->ml_set('Photo_gallery/galleries/path', $id, $path),
			$this->ml_set('Photo_gallery/galleries/description', $id, $description),
			(int)(bool)$active,
			$preview_image,
			$id
		)) {
			$Cache	= $this->cache;
			unset(
				$Cache->{"galleries/$id"},
				$Cache->{'galleries/list'}
			);
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Delete specified gallery
	 *
	 * @param int	$id
	 *
	 * @return bool
	 */
	function del_gallery ($id) {
		$id		= (int)$id;
		if (!$this->db_prime()->q(
			"DELETE FROM `[prefix]photo_gallery_galleries`
			WHERE `id` = '%s'
			LIMIT 1",
			$id
		)) {
			return false;
		}
		$this->ml_del('Photo_gallery/galleries/title', $id);
		$this->ml_del('Photo_gallery/galleries/path', $id);
		$this->ml_del('Photo_gallery/galleries/description', $id);
		$Cache	= $this->cache;
		unset(
			$Cache->{"galleries/$id"},
			$Cache->{'galleries/list'}
		);
		$images	= $this->cdb()->qfas([
			"SELECT `id`
			FROM `[prefix]photo_gallery_images`
			WHERE `gallery` = '%s'",
			$id
		]);
		foreach ($images as $image) {
			$this->del($image);
		}
		@$this->storage()->rmdir("Photo_gallery/$id");
		return true;
	}
	private function ml_process ($text, $auto_translation = true) {
		return Text::instance()->process($this->cdb(), $text, $auto_translation);
	}
	private function ml_set ($group, $label, $text) {
		return Text::instance()->set($this->cdb(), $group, $label, $text);
	}
	private function ml_del ($group, $label) {
		return Text::instance()->del($this->cdb(), $group, $label);
	}
}
