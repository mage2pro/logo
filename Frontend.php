<?php
namespace Dfe\Logo;
use Df\Core\Exception as DFE;
use Dfe\Logo\M\R\Value as Rc;
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
	 * @return string
	 * @throws DFE|LE
	 */
	final protected function _toHtml() {
		$p = df_registry('product'); /** @var Product $p */
		$mc = df_o(MC::class); /** @var MC $mc */
		$rc = df_o(Rc::class); /** @var Rc $rc */
		return !($images = $rc->getImagesWithOptionIdAndTitle($p->getId())) ? '' : df_cc_n(
			df_tag('div',
				['class' => 'dfe-logo']
				+ df_widget($this, 'main', [
					'left' => $p['dfe_logo_offset_left'] ?: 0
					,'optionId' => dfa(df_first($images), 'option_id')
					,'scale' => $p['dfe_logo_scale'] ?: 0.4
					,'top' => $p['dfe_logo_offset_top'] ?: 0
				])
				, df_cc_n(df_map($images, function(array $i) use($mc) {return
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