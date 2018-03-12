<?php
namespace Dfe\Logo;
use Df\Core\Exception as DFE;
use Dfe\Logo\Model\Value as V;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Media\Config as MC;
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
	 * @throws DFE
	 */
	final protected function _toHtml() {
		$v = df_new_om(V::class); /** @var V $v */
		$p = df_registry('product'); /** @var Product $p */
		$mc = df_o(MC::class); /** @var MC $mc */
		return df_cc_n(
			df_tag('div', ['class' => 'dfe-logo'] + df_widget($this, 'main', []), df_cc_n(
				df_map_k($v->getImages($p->getId()), function($id, $image) use($mc) {return
					df_tag('img', ['data-id' => $id, 'src' => $mc->getMediaUrl($image)])
				;})
			))
			,df_link_inline(df_asset_name(null, $this, 'css'))
		);
	}
}