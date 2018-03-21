<?php
namespace Dfe\Logo\Plugin\Catalog\Block\Product\View;
use Dfe\Logo\M\R\Value as Rc;
use Magento\Catalog\Block\Product\View\Options as Sb;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option as O;
// 2018-03-13
final class Options {
	/**
	 * 2018-03-13
	 * @see \Magento\Catalog\Block\Product\View\Options::getOptionHtml()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param O $o
	 * @return string
	 */
	function aroundGetOptionHtml(Sb $sb, \Closure $f, O $o) {
		$r = $f($o);
		if ($this->optionId() === (int)$o->getId()){
			$r = df_tag('div', ['style' => 'display:none'], $r);
		}
		return $r;
	}

	/**
	 * 2018-03-13
	 * @return int
	 */
	function optionId() {return dfc($this, function() {
		$rc = df_o(Rc::class); /** @var Rc $rc */
		$p = df_registry('product'); /** @var Product $p */
		return (int)dfa(df_eta(df_first($rc->getImagesWithOptionId($p->getId()))), 'option_id');
	});}
}

