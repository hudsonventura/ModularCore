<?php
namespace ModularCore;
if (!defined('ROOT_ACCESS')) exit('<h2>ERROR 403 - FORBIDDEN</h2> You can\'t access this page');


	





class TinyMCE {
	
	public static $inline;
	
	function __construct(){

		
		self::$inline = '
		
		';
	}
	
	public static function inline($value = null, $name){?>
		<script src="<?php echo BASEDIR?>core/libs/tinymce/tinymce.min.js"></script>
		<script type="text/javascript">
			tinymce.init({
				selector: "h1.editable",
				inline: true,
				toolbar: "undo redo",
				menubar: false
			});
			
			tinymce.init({
				selector: "div.editable",
				inline: true,
				plugins: [
					"advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table contextmenu directionality",
					"emoticons template paste textcolor colorpicker textpattern imagetools"

				],
				toolbar1: "fontselect bold italic underline strikethrough fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | styleselect formatselect",
				toolbar2: "cut copy paste  | bullist numlist | outdent indent blockquote | undo redo | link unlink image media code | insertdatetime preview",
				toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

			});
		</script>
		<?php $id = rand(0, 999); ?>
		<form method="POST" action="">
			<div class="editable" id="<?php echo $name?>" style="width:100%; height:100%">
				<?php if(isset($value)){echo $value;}else{ echo 'This area can be edited!';}?>
			</div>
			<button type="submit" submit="" class="btn btn-primary">Save</button>
		</form>

	<?php
	}
	
	
}
