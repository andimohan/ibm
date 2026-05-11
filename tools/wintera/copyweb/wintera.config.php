<?php 

// file2 pasti dicopy

// file template
$arrFiles = array();
array_push($arrFiles,'assets/');
array_push($arrFiles,'connections/_connection.php');
array_push($arrFiles,'connections/'.DOMAIN_NAME.'.php');
array_push($arrFiles,'personalized/'.DOMAIN_NAME.'/');
array_push($arrFiles,'personalized/'.DOMAIN_NAME.'/');
array_push($arrFiles,'phpthumb/');
array_push($arrFiles,'template/'.DOMAIN_NAME.'/');
array_push($arrFiles,'Twig/');
array_push($arrFiles,'uploadeditor/'.DOMAIN_NAME.'/');

// CSS & Others

array_push($arrFiles,'include/css-1.4/');
array_push($arrFiles,'include/fonts/');
array_push($arrFiles,'include/img/');
array_push($arrFiles,'include/lang/');

// JS 
array_push($arrFiles,'include/js-1.9/formJS-1.115.min.js'); // ini berubah terus
array_push($arrFiles,'include/js-1.9/main-3.76.min.js');

array_push($arrFiles,'include/js-1.9/api.min.js');
array_push($arrFiles,'include/js-1.9/bootstrap.min.js');
array_push($arrFiles,'include/js-1.9/bootstrapValidator.js');
array_push($arrFiles,'include/js-1.9/ckeditor-4.11.3/');
array_push($arrFiles,'include/js-1.9/ckfinder/');
array_push($arrFiles,'include/js-1.9/clock.js');
array_push($arrFiles,'include/js-1.9/fileuploader.min.js');
array_push($arrFiles,'include/js-1.9/freeze-table.js');
array_push($arrFiles,'include/js-1.9/imprint.min.js');
array_push($arrFiles,'include/js-1.9/jquery-3.3.1.min.js');
array_push($arrFiles,'include/js-1.9/jquery-scrollToTop.js');
array_push($arrFiles,'include/js-1.9/jquery-ui-timepicker-addon.min.js');
array_push($arrFiles,'include/js-1.9/jquery-ui.min.js');
array_push($arrFiles,'include/js-1.9/jquery.contextMenu.min.js');
array_push($arrFiles,'include/js-1.9/jquery.cookie.min.js');
array_push($arrFiles,'include/js-1.9/jquery.formatCurrency-1.4.0.min.js');
array_push($arrFiles,'include/js-1.9/jquery.metadata.min.js');
array_push($arrFiles,'include/js-1.9/jquery.mmenu.min.all.js');
array_push($arrFiles,'include/js-1.9/jquery.shuffle.min.js');
array_push($arrFiles,'include/js-1.9/jsapi.js');
array_push($arrFiles,'include/js-1.9/login.min.js');
array_push($arrFiles,'include/js-1.9/moment.min.js');
array_push($arrFiles,'include/js-1.9/pace.min.js');
array_push($arrFiles,'include/js-1.9/php-variables-1.2.min.js');
array_push($arrFiles,'include/js-1.9/report-2.11.min.js');
array_push($arrFiles,'include/js-1.9/sol.js');

// =================== CLASS
// compro 


// ganti pake dummy
//array_push($arrFiles,'include/'.CLASS_VERSION.'/Marketplace.class.php');
//array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemChecklist.class.php');
//array_push($arrFiles,'include/'.CLASS_VERSION.'/ChartOfAccount.class.php');


array_push($arrFiles,'include/'.CLASS_VERSION.'/Banner.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/BaseClass.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/AutoCode.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Article.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ArticleCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Category.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/City.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/CityCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Company.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Contact.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/CustomCode.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Employee.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/EmployeeCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Division.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Exception.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/JobOpportunities.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Lang.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/LoginLog.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Page.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Partners.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Gallery.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ManagementTeam.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Portfolio.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/PortfolioCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/News.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/NewsCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/minerva.lc'); 
array_push($arrFiles,'include/'.CLASS_VERSION.'/WidgetSetting.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Testimonial.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Warehouse.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/SMTP.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Mobile_Detect.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/PHPMailer.class.php');

// ecommerce
array_push($arrFiles,'include/'.CLASS_VERSION.'/CustomerCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Customer.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Brand.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/COALink.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/DiscountScheme.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Download.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/DownloadCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/EmailBlast.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Excel.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/FilterCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Item.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemCondition.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemFilter.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemIn.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemInReceive.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemMovement.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemOut.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemOutDelivery.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemPackage.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemPromo.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ItemUnit.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Location.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/RoleTemplate.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Service.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/ServiceCategory.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/SocialMedia.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Supplier.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Tag.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/TemplateCustomer.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/TemplateSupplier.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/WarehouseTransfer.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/Youtube.class.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/php-mailjet-events.class-mailjet-0.1.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/php-mailjet.class-mailjet-0.1.php');
array_push($arrFiles,'include/'.CLASS_VERSION.'/xmlapi.class.php');

// =================== ADMIN
// compro
array_push($arrFiles,'admin/_populateData.php');
array_push($arrFiles,'admin/index.php');
array_push($arrFiles,'admin/list.php');
array_push($arrFiles,'admin/dataList.php');
array_push($arrFiles,'admin/menu.php'); 
array_push($arrFiles,'admin/ajax-widget-setting.php');
array_push($arrFiles,'admin/ajax-disk-usage.php');
array_push($arrFiles,'admin/ajax-tag.php');
array_push($arrFiles,'admin/ajax-role-template.php');
array_push($arrFiles,'admin/ajax-news-category.php');
array_push($arrFiles,'admin/ajax-warehouse.php');
array_push($arrFiles,'admin/ajax-notification.php');
array_push($arrFiles,'admin/ajax-download-category.php');
array_push($arrFiles,'admin/ajax-city.php');
array_push($arrFiles,'admin/ajax-city-category.php');
array_push($arrFiles,'admin/ajax-custom-code.php');
array_push($arrFiles,'admin/ajax-2fauth.php');
array_push($arrFiles,'admin/ajax-general.php');
array_push($arrFiles,'admin/ajax-employee.php');
array_push($arrFiles,'admin/ajax-login.php');
array_push($arrFiles,'admin/ajax-article-category.php');
array_push($arrFiles,'admin/ajax-service-category.php');
array_push($arrFiles,'admin/galleryList.php');
array_push($arrFiles,'admin/galleryForm.php');
array_push($arrFiles,'admin/partnersForm.php');
array_push($arrFiles,'admin/employeeList.php');
array_push($arrFiles,'admin/employeeCategoryForm.php');
array_push($arrFiles,'admin/employeeCategoryList.php');
array_push($arrFiles,'admin/cityCategoryForm.php'); 
array_push($arrFiles,'admin/cityCategoryList.php');
array_push($arrFiles,'admin/warehouseForm.php');
array_push($arrFiles,'admin/warehouseList.php');
array_push($arrFiles,'admin/serviceForm.php');
array_push($arrFiles,'admin/serviceList.php');
array_push($arrFiles,'admin/bannerForm.php');
array_push($arrFiles,'admin/bannerList.php'); 
array_push($arrFiles,'admin/history.php');
array_push($arrFiles,'admin/updateProfile.php'); 
array_push($arrFiles,'admin/getDashboardData.php'); 
array_push($arrFiles,'admin/roleTemplateForm.php'); 
array_push($arrFiles,'admin/roleTemplateList.php');
array_push($arrFiles,'admin/cityForm.php'); 
array_push($arrFiles,'admin/cityList.php');
array_push($arrFiles,'admin/youtubeForm.php');
array_push($arrFiles,'admin/youtubeList.php'); 
array_push($arrFiles,'admin/testimonialForm.php');
array_push($arrFiles,'admin/testimonialList.php'); 
array_push($arrFiles,'admin/articleForm.php');
array_push($arrFiles,'admin/articleList.php');
array_push($arrFiles,'admin/articleCategoryForm.php');
array_push($arrFiles,'admin/articleCategoryList.php'); 
array_push($arrFiles,'admin/contactUsForm.php');
array_push($arrFiles,'admin/contactUsList.php');
array_push($arrFiles,'admin/newsForm.php');
array_push($arrFiles,'admin/newsList.php');
array_push($arrFiles,'admin/pageForm.php');
array_push($arrFiles,'admin/pageList.php'); 
array_push($arrFiles,'admin/serviceCategoryForm.php');
array_push($arrFiles,'admin/serviceCategoryList.php');
array_push($arrFiles,'admin/partnersList.php'); 
array_push($arrFiles,'admin/diskUsage.php');
array_push($arrFiles,'admin/customCodeForm.php');
array_push($arrFiles,'admin/customCodeList.php');
array_push($arrFiles,'admin/autoCodeForm.php');
array_push($arrFiles,'admin/getPHPErrorMsg.php');
array_push($arrFiles,'admin/logout.php');
array_push($arrFiles,'admin/autoCodeList.php');
array_push($arrFiles,'admin/dataProcess.php');
array_push($arrFiles,'admin/profile.php');
array_push($arrFiles,'admin/updateSetting.php');
array_push($arrFiles,'admin/fileuploader.php');
array_push($arrFiles,'admin/getPHPLang.php');
array_push($arrFiles,'admin/setting.php');
array_push($arrFiles,'admin/getPHPModuleSetting.php');
array_push($arrFiles,'admin/notification.php');
array_push($arrFiles,'admin/getPHPVariables.php');
array_push($arrFiles,'admin/getPHPConfiguration.php');
array_push($arrFiles,'admin/getPHPSetting.php');
array_push($arrFiles,'admin/summaryDashboard.php');
array_push($arrFiles,'admin/populateData.php');
array_push($arrFiles,'admin/.htaccess');

// pake gk pake

//array_push($arrFiles,'admin/downloadForm.php');
//array_push($arrFiles,'admin/downloadList.php');
//array_push($arrFiles,'admin/downloadCategoryForm.php');
//array_push($arrFiles,'admin/downloadCategoryList.php');
//array_push($arrFiles,'admin/eventForm.php');
//array_push($arrFiles,'admin/issueCategoryForm.php');
//array_push($arrFiles,'admin/issueCategoryList.php');
//array_push($arrFiles,'admin/ajax-issue-category.php');

// ecommerce

array_push($arrFiles,'admin/ajax-customer.php');
array_push($arrFiles,'admin/customerForm.php');
array_push($arrFiles,'admin/customerList.php');
array_push($arrFiles,'admin/customerCategoryForm.php');
array_push($arrFiles,'admin/customerCategoryList.php');

//array_push($arrFiles,'admin/ajax-supplier.php');
//array_push($arrFiles,'admin/supplierForm.php');
//array_push($arrFiles,'admin/supplierList.php');

// item
//array_push($arrFiles,'admin/ajax-item.php');
//array_push($arrFiles,'admin/ajax-item-category.php');
//array_push($arrFiles,'admin/ajax-item-package.php');
//array_push($arrFiles,'admin/ajax-brand.php');
//array_push($arrFiles,'admin/storefrontForm.php');
array_push($arrFiles,'admin/itemForm.php');
array_push($arrFiles,'admin/itemList.php');
//array_push($arrFiles,'admin/itemCategoryForm.php');
//array_push($arrFiles,'admin/itemCategoryList.php');
//array_push($arrFiles,'admin/itemConditionForm.php');
//array_push($arrFiles,'admin/itemConditionList.php');
//array_push($arrFiles,'admin/getItemImages.php');
// 
//array_push($arrFiles,'admin/ajax-shipping-company.php');
// 
//array_push($arrFiles,'admin/updateShipmentTracking.php');
//array_push($arrFiles,'admin/discountSchemeForm.php');
//array_push($arrFiles,'admin/discountSchemeList.php');
//array_push($arrFiles,'admin/populateDataCOA.php');
// 
//array_push($arrFiles,'admin/ajax-downpayment.php');
//array_push($arrFiles,'admin/storefrontList.php');


// pake gk pake

//array_push($arrFiles,'admin/companyForm.php'); 
//array_push($arrFiles,'admin/filterCategoryList.php');
//array_push($arrFiles,'admin/bugList.php');
//array_push($arrFiles,'admin/bugForm.php');
//array_push($arrFiles,'admin/ajax-bom.php');
//array_push($arrFiles,'admin/filterCategoryForm.php');
//array_push($arrFiles,'admin/ajax-filter-category.php');
//array_push($arrFiles,'admin/companyList.php');
//array_push($arrFiles,'admin/eventList.php');



// =================== ROOT
// compro 
array_push($arrFiles,'_include.php');
array_push($arrFiles,'_include-v2.php'); 
array_push($arrFiles,'_include-fe-v2.php');
//array_push($arrFiles,'_include-min.php');
array_push($arrFiles,'_config.php');
array_push($arrFiles,'_global.php');
array_push($arrFiles,'_twig-function.php');
array_push($arrFiles,'ajax-general.php');
array_push($arrFiles,'ajax-lang.php');
array_push($arrFiles,'ajax-contact-us.php'); 
array_push($arrFiles,'fileuploader.php');

array_push($arrFiles,'page-content.php'); 
array_push($arrFiles,'index.php');
array_push($arrFiles,'articles.php');
array_push($arrFiles,'article-detail.php');
array_push($arrFiles,'news.php');
array_push($arrFiles,'news-detail.php');
array_push($arrFiles,'partners.php');
array_push($arrFiles,'services.php');
array_push($arrFiles,'service-category.php');
array_push($arrFiles,'service-detail.php');
array_push($arrFiles,'download.php'); 
array_push($arrFiles,'gallery.php'); 
array_push($arrFiles,'gallery-detail.php');
array_push($arrFiles,'popup-banner.php');
array_push($arrFiles,'popup-login.php');

array_push($arrFiles,'contact-us.php');
array_push($arrFiles,'testimonial.php');
array_push($arrFiles,'job-opportunities.php');

array_push($arrFiles,'searchCity.php');
array_push($arrFiles,'ajax-city.php');
array_push($arrFiles,'brand.php');
array_push($arrFiles,'under-maintenance.php');
array_push($arrFiles,'getPHPConfiguration.php');
array_push($arrFiles,'.gitignore');
array_push($arrFiles,'_personalized.php');
array_push($arrFiles,'error-page.php');
array_push($arrFiles,'page.php');
array_push($arrFiles,'.htaccess');
array_push($arrFiles,'minerva.lc');
//array_push($arrFiles,'robots.txt');
//array_push($arrFiles,'management-team.php');
//array_push($arrFiles,'ajax-testimonial.php');

//login  
array_push($arrFiles,'ajax-member.php');
array_push($arrFiles,'login.php');
array_push($arrFiles,'logout.php');
array_push($arrFiles,'update-password.php');
array_push($arrFiles,'profile.php');
array_push($arrFiles,'activation.php');
array_push($arrFiles,'resend-activation.php');
array_push($arrFiles,'account-recovery.php');
array_push($arrFiles,'registration.php');
array_push($arrFiles,'forgot-password.php');

// products list 
//array_push($arrFiles,'ajax-item.php');
//array_push($arrFiles,'products.php');
//array_push($arrFiles,'products-detail.php');
//array_push($arrFiles,'product-quick-view.php');  
//array_push($arrFiles,'products-search.php');
//array_push($arrFiles,'compare-products.php');

// cart
//array_push($arrFiles,'cart.php');
//array_push($arrFiles,'ajax-cart.php');
//array_push($arrFiles,'ajax-shipment.php');
//array_push($arrFiles,'ajax-payment-confirmation.php');
//array_push($arrFiles,'ajax-sales-order.php');
//array_push($arrFiles,'payment-confirmation.php');
//array_push($arrFiles,'payment-process.php');
//array_push($arrFiles,'transaction-history.php');

// pake gk pake
/*array_push($arrFiles,'exportCSV.php'); 
array_push($arrFiles,'invoice.php');
array_push($arrFiles,'voucher.php'); 
array_push($arrFiles,'download-list.php');*/

//array_push($arrFiles,'rewards-point.php'); 
//array_push($arrFiles,'ajax-emailblast.php');
//array_push($arrFiles,'products-preorder-detail.php');
//array_push($arrFiles,'report-item-movement-depot-detail.php');
//array_push($arrFiles,'report-item-movement-depot.php');
//array_push($arrFiles,'products-preorder.php');
//array_push($arrFiles,'report-monthly-summary.php');
//array_push($arrFiles,'employee-login.php');
//array_push($arrFiles,'report-stock-depot-card.php');
//array_push($arrFiles,'help.php');
//array_push($arrFiles,'checkin.php');
//array_push($arrFiles,'service-booking.php');
//array_push($arrFiles,'_report-config.php');
//array_push($arrFiles,'restock.php');

 
?>