<?php
namespace Dfe\Logo;
use Magento\Ui\DataProvider\Modifier\ModifierInterface as IModifier;
// 2018-03-12
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Modifier implements IModifier {
	/**
	 * 2018-03-12
	 * @override
	 * @see \Magento\Ui\DataProvider\Modifier\ModifierInterface::modifyData()
	 * @used-by \Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider::getData()
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 *	public function getData() {
	 *		foreach ($this->pool->getModifiersInstances() as $modifier) {
 	 *			$this->data = $modifier->modifyData($this->data);
	 *		}
	 *		return $this->data;
	 *	}
	 * https://github.com/magento/magento2/blob/2.1.9/app/code/Magento/Catalog/Ui/DataProvider/Product/Form/ProductDataProvider.php#L46-L57
	 * @see \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::modifyData():
	 * https://github.com/magento/magento2/blob/2.1.9/app/code/Magento/Catalog/Ui/DataProvider/Product/Form/Modifier/CustomOptions.php#L155-L188
	 * @param array $a
	 * @return array
	 */
	function modifyData(array $a) {return $a;}

	/**
	 * 2018-03-12
	 * @override
	 * @see \Magento\Ui\DataProvider\Modifier\ModifierInterface::modifyMeta()
	 * @used-by \Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider::getMeta():
	 * 	public function getMeta() {
	 *		$meta = parent::getMeta();
	 *		foreach ($this->pool->getModifiersInstances() as $modifier) {
	 *			$meta = $modifier->modifyMeta($meta);
	 *		}
	 *		return $meta;
	 *	}
	 * https://github.com/magento/magento2/blob/2.1.9/app/code/Magento/Catalog/Ui/DataProvider/Product/Form/ProductDataProvider.php#L59-L72  
	 * @see \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::modifyMeta():
	 * https://github.com/magento/magento2/blob/2.1.9/app/code/Magento/Catalog/Ui/DataProvider/Product/Form/Modifier/CustomOptions.php#L208-L218
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @param array $a
	 * @return array
	 */
	function modifyMeta(array $a) {
	/*	$s = &$a
			["custom_options"]["children"]["options"]["children"]["record"]["children"]["container_option"]
			["children"]["container_common"]["children"]["type"]["arguments"]["data"]["config"]
			["groupsConfig"]['select']['values']
		;
		$s[]= 'dfe_logo';
		$s2 = &$a["custom_options"]["children"]["options"]["children"]["record"]["children"]["container_option"]["children"]["container_type_static"]["children"];
		$s2['dfe_logo']["arguments"]["data"]["config"] = [
			'componentType' => 'field'
			,'dataScope' => 'df-logo'
			,'dataType' => 'text'
			,'formElement' => 'input'
			,'label' => __('Logotype')
			,'sortOrder' => 31
		];  */
		return $a;
	}
}