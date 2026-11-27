<?php
	 /**
	 * Nguyen Binh
	 */
	
	class Dir{
		public function getDirSize($directory) {
			if ( version_compare( phpversion(), '5', '<' ) )
				return $this->dirSizeOld($directory);
			else
				return $this->dirSize($directory);
		}
		private function dirSize($directory) { //This works with any platform which is having php 5 or higher version.
			$size = 0;
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
				$size+=$file->getSize();
			}
			return $size;
		} 
		
		private function dirSizeOld($directory) {
		  $total_size = 0;
		  $files = scandir($directory);
		
		  foreach($files as $t) {
			if (is_dir(rtrim($directory, '/') . '/' . $t)) {
			  if ($t<>"." && $t<>"..") {
				  $size = $this->dirSizeOld(rtrim($directory, '/') . '/' . $t);
				  $total_size += $size;
			  }
			} else {
			  $size = filesize(rtrim($directory, '/') . '/' . $t);
			  $total_size += $size;
			}
		  }
		  return $total_size;
		}
		
		public function format_size($size) {
		  $mod = 1024;
		  $units = explode(' ','B KB MB GB TB PB');
		  for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		  }
		
		  return round($size, 2) . ' ' . $units[$i];
		}	
		
		public function createDir($dir,$permission='0777'){
			if (!file_exists($dir)) {
				mkdir($dir, $permission, true);
			}
			return is_dir($dir) || mkdir($dir);	
		}
		
		public function deleteDir($dir){
			if (is_dir($dir) === true){
				$files = array_diff(scandir($dir), array('.', '..'));
				foreach ($files as $file){
					Delete(realpath($dir) . '/' . $file);
				}
				return rmdir($dir);
			}
		
			else if (is_file($dir) === true){
				return unlink($dir);
			}
			return false;
		}
		
		public function copyDir($source, $dest){
		   if (is_file($source)) {
			  return copy($source, $dest);
		   }
		   if (!is_dir($dest)) {
			  mkdir($dest);
			  $company = ($_POST['company']);
		   }
		   $dir = dir($source);
		   while (false !== $entry = $dir->read()) {
			  if ($entry == '.' || $entry == '..') {
				 continue;
			  }
		
			  if ($dest !== "$source/$entry") {
				 $this->copyDir("$source/$entry", "$dest/$entry");
			  }
		   }
		
		   $dir->close();
		   return true;
		}
		
		public function checkPermissionDir($dir){
			$filename = $dir;
			if (is_writable($filename)) {
				return true;
			} else {
				return false;
			}	
		}
	}
?>