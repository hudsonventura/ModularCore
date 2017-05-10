<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


class Make  extends Core{


    function __construct(){
        parent::__construct();
    }
    
    public function link($text, $target, $class='', $id='', $adicional=''){
    	if(strpos($target, 'javascript')> -1){
    		return "<a href='$target' class='$class' id='$id' $adicional>$text</a>";
    	}
        return "<a href='$target' class='$class' id='$id' $adicional>$text</a>";
    }
    
    public function table($titles, $content, $class='', $id='', $adicional=''){
	
        $table = '';
        $table = $table."<table class='$class' id='$id' $adicional>
                <thead>
                    <tr>";
        
        foreach($titles as $title){
            $table = $table."<th>
                    $title
                </th>";
        }
                        
                        
        $table = $table."       </tr>
                            </thead>
                            <tbody>";
        
        foreach($content as $row){
            $table = $table."<tr>";
            foreach($row as $column){
                $table = $table."<td>";
                $table = $table.$column;
                $table = $table."</td>";
            }
            $table = $table."</tr>";   
        }
                        
                        
        $table = $table.       "</tr>
                </tbody>
            </table>";
        
        return $table;
    }
}


