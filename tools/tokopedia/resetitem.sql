truncate item;
TRUNCATE TABLE `item_marketplace_sync_detail`;
TRUNCATE TABLE `item_marketplace_link`;

delete from item_category where systemVariable <> 1;
TRUNCATE TABLE `item_category_storefront`;
TRUNCATE TABLE `item_category_storefront_detail`;
TRUNCATE TABLE `item_category_marketplace_detail`;
TRUNCATE TABLE `item_category_marketplace_attributes`;

 