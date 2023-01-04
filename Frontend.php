<?php
namespace Dfe\Logo;
use Df\Core\Exception as DFE;
use Dfe\Logo\R\Value as Rc;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Media\Config as MC;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\View\Element\AbstractBlock as _P;
// 2018-03-13
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Frontend extends _P {
	/**
	 * 2018-03-13
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 * @throws DFE|LE
	 */
	final protected function _toHtml():string {
		$p = df_registry('product'); /** @var Product $p */
		$mc = df_o(MC::class); /** @var MC $mc */
		$rc = df_o(Rc::class); /** @var Rc $rc */
		/**
		 * 2018-03-21
		 * @param array(string => mixed) $i
		 * @param int|float $d [optional]
		 * @return array(string => int|float)
		 */
		$logoF = function(array $i, string $k, $d = 0) use($p):array {return [$k => floatval(
			/**
			 * 2018-03-31
			 * 1) We should not use the 3-rd parameter of @uses dfa()
			 * because it will not used in the 0 case.
			 * «If I set global value its not working in any image»:
			 * https://github.com/mage2pro/logo/issues/1
			 * https://www.upwork.com/messages/rooms/room_b509c3b37b15c72c500ce79a2a9568c4/story_8d6ec5e96d68a60e4d313b3182a085a4
			 * 2) @uses floatval() is required here too, because `'0.00' ?: 3` will return '0.00'.
			 */
			floatval(dfa($i, "logo_$k")) ?: (floatval(dfa($i, "logo_{$k}_default")) ?: (
				floatval($p['scale' === $k ? "dfe_logo_$k" : "dfe_logo_offset_$k"]) ?: $d
			))
		)];};
		$logos = df_eta(dfa(df_eta($p['media_gallery']), 'images'));
		return !($images = $rc->getImagesWithOptionIdAndTitle($p->getId())) ? '' : df_cc_n(
			df_tag('div',
				['class' => 'dfe-logo']
				+ df_widget($this, 'main', [
					'logoId' => !$logos ? null : df_first($logos)['value_id']
					,'logos' => df_map(function(array $i) use($logoF):array {return
						$logoF($i, 'left') + $logoF($i, 'top') + $logoF($i, 'scale', 0.4)
						+ ['position' => intval(dfa($i, 'position')) ?: (intval(dfa($i, 'position_default')) ?: 0)]
					;}, $logos)
					,'optionId' => dfa(df_first($images), 'option_id')
				])
				, df_cc_n(df_map($images, function(array $i) use($mc):string {return
					df_tag('div', null, df_cc_n(
						df_tag('img', [
							'alt' => $i[Rc::K_TITLE]
							,'data-id' => $i['option_type_id']
							,'src' => $mc->getMediaUrl($i['image'])
						])
						,df_tag('div', null, $i[Rc::K_TITLE])
					))
				;}))
			), df_link_inline(df_asset_name(null, $this, 'css'))
		);
	}
}