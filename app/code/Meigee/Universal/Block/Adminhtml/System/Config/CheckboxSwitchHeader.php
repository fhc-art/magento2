<?php

namespace Meigee\Universal\Block\Adminhtml\System\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CheckboxSwitchHeader extends \Magento\Config\Block\System\Config\Form\Field
{

	private function _getCss()
    {

	return '<style>
		#universal_general_universal_skins tr:not(#row_universal_general_universal_skins_site_skin) {display: none;}
		#universal_general_universal_skins tr:not(#row_universal_general_universal_skins_site_skin) .onoffswitch-label {display: none;}
	</style>';
    }

	private function _getJs()
    {
	return '
<script type="text/javascript">//<![CDATA[
		require(["jquery"], function(jQuery)
		{
			jQuery(document).ready( function()
			{
				function UncheckAll(){
					var w = jQuery("#universal_general_universal_skins tr:not(#row_universal_general_universal_skins_site_skin)").find(".use-default .checkbox:checked");
					for(var i = 0; i < w.length; i++){
						if(w[i].type=="checkbox"){
							//w[i].checked = false;
							jQuery(w[i]).trigger("click");
						}
					}
				}
				UncheckAll();
				/* default_checkbox = jQuery("#universal_general_universal_skins tr:not(#row_universal_general_universal_skins_site_skin)").find(".use-default .checkbox");
				setTimeout(function(){
					default_checkbox.trigger("click");
				}, 1000); */
				setTimeout(function(){
					currentName = jQuery("#row_universal_general_universal_skins_site_skin .meigee-thumb-radio .meigee-thumb-horizontal > label.active input").attr("value");
					currentName = currentName.slice(0, -4);
					jQuery("#universal_general_universal_skins tr:not(#row_universal_general_universal_skins_site_skin)").each(function(){
						curr_input = jQuery(this).find(".onoffswitch input:not(.onoffswitch-checkbox)");
						curr_input_id = curr_input.attr("id");
						if("undefined" != curr_input_id) {
							startIndex = curr_input_id.indexOf("skinheader_") + 11;
							endIndex = curr_input_id.length;
							currentNameLength = currentName.length;

							if((curr_input_id.indexOf(currentName) + 1) && ((endIndex - startIndex) == currentNameLength)){
								jQuery("#"+curr_input_id).val("1");
							} else {
								jQuery("#"+curr_input_id).val("0");
							}
						}
					});

				}, 200);
				jQuery("#row_universal_general_universal_skins_site_skin .meigee-thumb-radio .meigee-thumb-horizontal").on("click", function()
				{
					// jQuery("#universal_general_universal_skins tr#row_universal_general_universal_skins_skinheader_fashion2").find("input.checkbox").trigger("click");
					name = jQuery(this).find("input").attr("value");
					name = name.slice(0, -4);
					jQuery("#universal_general_universal_skins tr:not(#row_universal_general_universal_skins_site_skin)").each(function(){
						// jQuery(this).find("input.checkbox").trigger("click");
						curr_input = jQuery(this).find(".onoffswitch input:not(.onoffswitch-checkbox)");
						curr_input_id = curr_input.attr("id");
						if("undefined" != curr_input_id) {
							startIndex = curr_input_id.indexOf("skinheader_") + 11;
							endIndex = curr_input_id.length;
							nameLength = name.length;

							if((curr_input_id.indexOf(name) + 1) && ((endIndex - startIndex) == nameLength)){
								jQuery("#"+curr_input_id).val("1");
							} else {
								jQuery("#"+curr_input_id).val("0");
							}
						}
					});
				})
			});
		})
//]]></script>';
    }

    protected function _getElementHtml(AbstractElement $element)
    {
		$curr_skin_option = $this->_scopeConfig->getValue('universal_general/universal_skins', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
		$curr_skin = substr($curr_skin_option['site_skin'], 0, -4);
		$curr_id = substr(strstr($element->getId(), 'skinheader_'), strlen('skinheader_'));
		$curr_head = strpos($curr_id, $curr_skin);
		$checked = '';
		$curr_header = str_replace(' value="1"', ' value="0"', $element->getElementHtml());
		// var_dump($curr_skin_option);
		// echo '\r';
		// var_dump($curr_skin);
		// echo '\r';
		// var_dump($curr_id);
		// if($curr_skin == $curr_id){
			// $curr_header = str_replace(' value="0"', ' value="1" checked="checked"', $element->getElementHtml());
		// } else {
			// $curr_header = str_replace(' value="1"', ' value="0"', $element->getElementHtml());
		// }

		$element->setType('text');
		$element->setChecked(true);
        return $this->_getJs() . $this->_getCss() .'

                <div class="onoffswitch">
		    <input id="'.$element->getId().'_onoffswitch" class="onoffswitch-checkbox" type="text" data-useid="'.$element->getId().'" />
                    <label class="onoffswitch-label" for="'.$element->getId().'_onoffswitch">
                        <span class="onoffswitch-inner">
							<span class="label-on">'.$this->getOnLabel().'</span>
							<span class="label-off">'.$this->getOffLabel().'</span>
						</span>
                        <span class="onoffswitch-switch"></span>
                    </label>

                      '.$curr_header.'
                </div>';
    }


}
