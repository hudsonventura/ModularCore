<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


class Pagination  extends Core{


	function __construct(){
		parent::__construct();
	}

	public function render($totalItems, $currentPage, $division, $compact = null, $pageLink, $firstName = 'First', $previousName = 'Prev.', $nextName = 'Next', $lastName = 'Last'){
		
		$array = array();
		/*FIRST*/
		if($firstName){
			if($currentPage==1){
				$active = 'disabled';
			}else{
				$active = '';
			}
			$temp = array(1, $firstName, $active);
			array_push($array, $temp);
		}/*FIRST*/
			
		/*PREVIOUS*/
		if($previousName){
			if($currentPage==1){
				$active = 'disabled';
			}else{
				$active = '';
			}
			$temp = array($currentPage-1,$previousName, $active);
			array_push($array, $temp);
		}/*PREVIOUS*/
		
		
		
		
		//MAIN
		if($compact){
			//$temp = array(null, '...', 'disabled');
			//array_push($array, $temp);
			
			for($i=$currentPage-$compact; $i<= $currentPage+$compact; $i++){
				if($i == $currentPage){
					$current = array($i, $currentPage, 'active');
					array_push($array, $current);
				}else{
					if($i >0 && $i<=ceil($totalItems/$division)){
						array_push($array, $i);
					}
				}
			}
			
			//$temp = array(null, '...', 'disabled');
			//array_push($array, $temp);
		}else{
			for($i=1; $i<= ceil($totalItems/$division); $i++){
				if($i == $currentPage){
					$current = array($i, $currentPage, 'active');
					array_push($array, $current);
				}else{
					if($i >0 && $i<=ceil($totalItems/$division)){
						array_push($array, $i);
					}
				}
			}
		}//MAIN

		
		
		
		
		/*NEXT*/
		if($nextName){
			if($currentPage>=ceil($totalItems/$division)){
				$active = 'disabled';
			}else{
				$active = '';
			}
			$temp = array($currentPage+1, $nextName, $active);
			array_push($array, $temp);
		}/*NEXT*/
		
		/*LAST*/
		if($lastName){
			if($currentPage>=ceil($totalItems/$division)){
				$active = 'disabled';
			}else{
				$active = '';
			}
			$temp = array(ceil($totalItems/$division), $lastName, $active);
			array_push($array, $temp);
		}/*LAST*/
		
		
		
		
		$return = '
				<nav>
					<ul class="pagination">
						<li>';
		foreach($array as $item){
			if(!is_array($item)){
				$return.= "<li><a href='$pageLink$item'>$item</a></li>";
			}else{
				if($item[2] == 'disabled'){
					$return.= '<li class='.$item[2].'><a>'.$item[1].'</a></li>';
				}else{
					$return.= '<li class='.$item[2].'><a href="'.$pageLink.$item[0].'">'.$item[1].'</a></li>';
				}
			}
		}
		$return.= '
				</li>
			</ul>
		</nav>';
		
		return $return;
	}


}


