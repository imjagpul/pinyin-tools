<?php
class SearchBoxWidget extends CWidget
{
	public function run()
	{
				//@TODO implement
				//active form does not make much sense here, so we put just normal form
		?>
		
		<div class="search">
    		<form action="<?php echo Yii::app()->createUrl("//char/lookup"); ?>" method="get">
    		<input type="text" name="s" value="" class="full" />
    		<input type="submit" value="Lookup character" />
    	    </form>
    	</div>		 
		
		<?php 				
	    }
		
}