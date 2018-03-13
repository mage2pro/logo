A product logotype chooser for Magento 2.

## How to install
```
composer require mage2pro/logo:*
bin/magento setup:upgrade
rm -rf pub/static/* && bin/magento setup:static-content:deploy en_US <additional locales, e.g.: de_DE>
rm -rf var/di var/generation generated/code && bin/magento setup:di:compile
```
If you have some problems while executing these commands, then check the [detailed instruction](https://mage2.pro/t/263).

## Frontend

![](https://mage2.pro/uploads/default/original/2X/0/0f549582cdfad0bcc858de55340e3d6044417551.png)

![](https://mage2.pro/uploads/default/original/2X/f/fbe2b2e52c0520ba6fa5d830a53e6d735ff33db2.png)

## Backend

You can set logotypes for a product in the «**Customizable Options**» section of the product's backend page.  
If you use configurable products, then set logotypes for the parent product only, not for children.  

![](https://mage2.pro/uploads/default/original/2X/c/c575f3ea0f3a4a24c330447fa2316f5644806574.png)

To control the logotypes appearance you should create **3 product attributes** with the following codes:

- `dfe_logo_offset_left`
- `dfe_logo_offset_top`
- `dfe_logo_scale`

![](https://mage2.pro/uploads/default/original/2X/4/49d720d69356dd74a2301c5b9f5bec816701b190.png)

These attributes should be **text fields**:
![](https://mage2.pro/uploads/default/original/2X/c/c9fb78c2d5c2f40bff80247712ddba0da08adc5b.png)

You should add these attributes to your **attribute set**:
![](https://mage2.pro/uploads/default/original/2X/9/97ca2c5f7ef2501f53f417307062d0ca6b646933.png)

Then the attributes will be should on your backend product page.  
The `dfe_logo_offset_left` and `dfe_logo_offset_top` attributes specify the chosen logotype's offset relative the top left corner of the main product image.  
The `dfe_logo_scale` attibutes specifies the scale factor of the chosen logotype. E.g. the `0.4` value means that the chosen logotype will have 40% of its initial size. 
![](https://mage2.pro/uploads/default/original/2X/b/b992161a9c1733a0c97622d7fe267657114da75e.png)





