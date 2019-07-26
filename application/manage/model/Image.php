<?php
	namespace app\manage\Model;

	use think\Model;

	Class Image extends Model
	{
		public function add_image($img,$title,$id,$route,$url='')
		{
			$imgName = base64_upload($img,$route);

			if($imgName){

				$imgId = Image::create([
		            'image_surface_name'      	=>  	$title,
		            'image_surface_id'      	=> 		$id,
		            'image_path'				=>		$route,
		            'image_name'				=>		$imgName,
		            'image_is_disable'			=>		1,
		            'image_url'					=>		$url,
		            'image_sort'				=>		1,
		        ],true);
		        return $imgId->image_id;
			}else{
				return false;
			}
		}

		public function findImg_deleteImg_addImg($img,$title,$id,$route,$url='')
		{
			$image_array = $this->findImg($title,$id);

			if( !empty($image_array)){
				$this->deleteImg($image_array);
			}
			$res = $this->add_image($img,$title,$id,$route,$url='');
			return $res;
		}
		public function findImg($title,$id)
		{
			$image_array = Image::where('image_surface_name',$title)->where('image_surface_id',$id)->find();

			return $image_array;
		}
		public function deleteImg($image_array)
		{
			$bool = unlink('./images/'.$image_array['image_path'].$image_array['image_name']);
			
			if($bool){
				$res = Image::destroy($image_array['image_id']);

				return $res;
			}else{
				return false;
			}
		}

		public function add_imagse($img,$title,$id,$route,$sort='1',$isurl='2',$url='',$isdis='1',$bz='')
		{
			$imgName = base64_upload($img,$route);

			if($imgName){

				$imgId = Image::create([
		            'image_surface_name'      	=>  	$title,
		            'image_surface_id'      	=> 		$id,
		            'image_path'				=>		$route,
		            'image_name'				=>		$imgName,
		            'image_sort'				=>		$sort,
		            'image_is_url'				=>		$isurl,
		            'image_is_disable'			=>		$isdis,
		            'image_url'					=>		$url,
		            'image_bz'					=>		$bz,
		        ],true);
		        return $imgId->image_id;
			}else{
				return false;
			}
		}
	}