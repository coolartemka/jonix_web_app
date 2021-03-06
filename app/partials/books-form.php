<?php
session_start();
  if (isset($_SESSION['demo'])) {
    if($_SESSION['demo'] == md5(date("Ymd").'UserLoggedIn')) { ?>
      <div ng-controller="ModalCtrl">
        <button class="btn" style="position: absolute; top: 8px; right: 10px" ng-click="logout()">{{'_Logout_' | i18n}}</button>
      </div>

<div class="row" ng-controller="MessageCtrl">
  <div class="span8">
    <div class="well">
      <form name="messageForm">
        <fieldset>
          <legend>{{'_Header_' | i18n}}</legend>

          <div class="row">
            <div class="span7">
              <h4>{{'_sender_' | i18n }}</h4>
            </div>
          </div>

          <div class="row">
            <label for="senderIdType" class="span2">{{'_sender_id_value_' | i18n}}</label>
            <div class="span5">
              <select id="senderIdType" ng-model="message.header.sender.senderIdentifier.senderIDType"
                  ng-options="key as value for (key, value) in nameCodeTypeList"
                  ng-init="message.header.sender.senderIdentifier.senderIDType='15'" required
                  id="senderIDType">
              </select>
              <input type="text" ng-model="message.header.sender.senderIdentifier.IDValue" class="form-control"
                id="senderIDValue" name="senderIDValue" placeholder="{{'_ID_value_' | i18n}}"
                maxlength=30 ng-pattern="senderIDValuePattern"
                tooltip="{{('_IDType_' + message.header.sender.senderIdentifier.senderIDType + '_') | i18n}}"
                tooltip-trigger="focus" tooltip-placement="right" />
              <p>
                <small class="text-error" ng-show="messageForm.senderIDValue.$error.pattern">
                  {{'_Tunnus_should_be_' | i18n}}
                </small>
              </p>
            </div>
          </div>

          <div class="row">
            <label for="sender-name" class="span2">{{'_senderName_' | i18n}}</label>
          	<div class="span5">
              <input type="text" ng-model="message.header.sender.senderName" class="form-control"
                name="senderName" id="senderName" placeholder="{{'_senderName_' | i18n}}"
                maxlength=30 ng-pattern="/^([A-Za-zÖöÄäÅå' ]{0,30})$/" />
              <p>
                <small class="text-error" ng-show="messageForm.senderName.$error.pattern">
                  {{'_just_letters_' | i18n}}
                </small>
              </p>
            </div>
          </div>

          <hr />

          <div class="row">
            <div class="span7">
              <h4>{{'_addressee_' | i18n }}</h4>
            </div>
          </div>

          <div class="row">
            <label for="addresseeIDType" class="span2">{{'_addressee_id_' | i18n }}</label>
            <div class="span5">
              <select id="addresseeIDType" ng-model="message.header.addressee.addresseeIdentifier.addresseeIDType"
                ng-options="key as value for (key, value) in nameCodeTypeList"
                ng-init="message.header.addressee.addresseeIdentifier.addresseeIDType='15'">
              </select>
              <input type="text" ng-model="message.header.addressee.addresseeIdentifier.IDValue"
                class="form-control" name="addresseeIDValue" placeholder="{{'_ID_value_' | i18n}}"
                maxlength=30 ng-pattern="addresseeIDValuePattern" id="addresseeIDValue"
                tooltip="{{('_IDType_' + message.header.addressee.addresseeIdentifier.addresseeIDType + '_') | i18n}}"
                tooltip-trigger="focus" tooltip-placement="right" />
              <p>
                <small class="text-error" ng-show="messageForm.addresseeIDValue.$error.pattern">
                  {{'_Tunnus_should_be_' | i18n}}
                </small>
              </p>
            </div>
          </div>

          <div class="row">
            <label for="addresseeName" class="span2">{{'_addressee_name_' | i18n }}</label>
            <div class="span5">
              <input type="text" ng-model="message.header.addresseeName" class="form-control"
                name="addresseeName" />
            </div>
          </div>

          <!-- Id ends -->

          <hr />

          <!-- TODO: get todays -->
          <div class="row" ng-init="setupDates()">
          	<label for="date-picker" class="span2">{{'_Date_' | i18n}}</label>
          	<div class="span5">

  	          <div ng-controller="DatepickerCtrl">
      			    <div class="form-horizontal">
      			        <input type="text" id="date-picker" class="input-small" datepicker-popup="yyyyMMdd"
                      ng-model="times.sentDate" is-open="opened" min="minDate"
                      max="'2015-06-22'" datepicker-options="dateOptions"
                      date-disabled="disabled(date, mode)" ng-required="true"
                      ng-change="updateSentDateTime();"/>
      			        <button class="btn btn-small btn-inverse" ng-click="today()">{{'_Today_' | i18n}}</button>
      			        <button class="btn btn-small btn-danger" ng-click="clear()">{{'_Clear_' | i18n}}</button>
                    <code>{{message.header.sentDateTime }}</code>
      			    </div>
      			  </div>

              <div class="checkbox">
              	<label>
                  <input type="checkbox" ng-model="showTime" ng-init="showTime=false"
                    ng-change="updateSentDateTime()" />{{'_Select_time_' | i18n}}
                </label>
              </div>
            </div>
          </div>

          <div class="row" ng-show="showTime">
          	<label for="time" class="span2">{{'_Time_' | i18n}}</label>
          	<div class="span5">
              <div ng-controller="TimepickerCtrl" class="ng-scope">
              	<div ng-model="times.sentTime" ng-change="updateSentDateTime();"
                  class="well well-small" style="display:inline-block;">
			    	      <timepicker hour-step="1" minute-step="1" show-meridian="false"></timepicker>
			  	      </div>
              </div>
          	</div>
          </div>

      	  <div ng-repeat="productItem in message.product" ng-form="productForm">
      	  	<legend>{{'_Product_' | i18n}}</legend>

      	  	<div class="row">
  	      	  <label for="record-reference" class="span2">{{'_Record_reference_' | i18n}}</label>
  	      	  <div class="span5">
  	      	  	<input id="record-reference" type="text" ng-model="productItem.recordReference" required/>
  	      	  </div>
  	      	</div>

  	      	<div class="row">
  	      	  <label for="notification-type" class="span2">{{'_Notification_type_' | i18n}}</label>
  	      	  <div class="span5">
  	      	  	<select id="notification-type" ng-model="productItem.notificationType"
                  ng-options="key as value for (key, value) in productNotificationTypeList"
                   required>
  	      	  	  <option value="">{{'_Select_notification_type_' | i18n}}</option>
  	      	  	</select>
                <code>{{productItem.notificationType}}</code>
  	      	  </div>
  	      	</div>

  	      	<div class="row">
  	      	  <label for="productIdType" class="span2">{{'_Product_ID_type_' | i18n}}</label>
  	      	  <div class="span5">
  	      	  	<select id="productIdType" name="productIdType" ng-model="productItem.productIdentifier.productIDType"
                  ng-options="key as value for (key, value) in productIdTypeList" required
                  ng-change="changeProductForm(productItem.productIdentifier.productIDType)" ng-init="productItem.productIdentifier.productIDType='02'">
  	      	  	  <option value="">{{'_Select_ID_type_' | i18n}}</option>
  	      	  	</select>
                <code>{{productItem.productIdentifier.productIDType}}</code>
                <input name="productIdValue" id="productIdValue" type="text" ng-model="productItem.productIdentifier.IDValue"
                  ng-pattern="productIdValuePattern($index)" required
                  tooltip="{{('_PIDType_' + productItem.productIdentifier.productIDType + '_') | i18n}}"
                  tooltip-trigger="focus" tooltip-placement="right"/>
                <p>
                  <small class="text-error" ng-show="productForm.productIdValue.$error.pattern">
                    {{'_Tunnus_should_be_' | i18n}}
                  </small>
                </p>
  	      	  </div>
  	      	</div>

  	      	<h4>{{'_Descriptive_details_' | i18n}}</h4>

  	      	<div class="row">
  	      	  <label for="product-composition" class="span2">{{'_Product_composition_' | i18n}}</label>
  	      	  <div class="span5">
  	      	  	<select id="product-composition" ng-model="productItem.descriptiveDetail.composition"
                    ng-options="key as value for (key, value) in productCompositionList" required>
                  <option value="">{{'_Select_composition_' | i18n}}</option>
                </select>
                <code>{{productItem.descriptiveDetail.composition}}</code>
  	      	  </div>
  	      	</div>

  	      	<div class="row">
  	      	  <label for="product-form" class="span2">{{'_Product_form_' | i18n}}</label>
  	      	  <div class="span5">
  	      	  	<select id="product-form" ng-model="productItem.descriptiveDetail.productForm"
                    ng-options="key as value for (key, value) in productFormList" required>
  	      	  		<option value="">{{'_Select_product_form_' | i18n}}</option>
  	      	  	</select>
                <code>{{productItem.descriptiveDetail.productForm}}</code>
  	      	  </div>
  	      	</div>

            <!-- TODO: make it multiplicable -->
  	      	<div class="row">
  	      	  <label class="span2">{{'_Product_title_' | i18n}}</label>
  	      	  <div class="span5">
  	      	  	<select type="text" ng-model="productItem.descriptiveDetail.titleDetail.titleType" ng-options="key as value for (key, value) in productTitleTypeList" required/>
                  <option value="">{{'_...title_type_' | i18n}}</option>
                </select>
                <code>{{productItem.descriptiveDetail.titleDetail.titleType}}</code>
                <select type="text" ng-model="productItem.descriptiveDetail.titleDetail.titleElement.titleElementLevel" ng-options="key as value for (key,value) in productTitleElementLevelList" required/>
                  <option value="">{{'_...element_level_' | i18n}}</option>
                </select>
                <code>{{productItem.descriptiveDetail.titleDetail.titleElement.titleElementLevel}}</code>
                <input type="text" ng-model="productItem.descriptiveDetail.titleDetail.titleElement.titleText" placeholder="{{'_title_text_' | i18n}}" required/>
                <span>[{{'_more..._' | i18n}}]</span>
  	      	  </div>
  	      	</div>

            <div class="row">
              <label class="span2">{{'_Language_' | i18n}}</label>
              <div class="span5">
                <select ng-model="productItem.descriptiveDetail.language.languageRole" ng-options="key as value for (key, value) in productLanguageRoleList" required/>
                  <option value="">{{'_...language_role_' | i18n}}</option>
                </select>
                <span ng-controller="TypeaheadCtrl" id="languageTypeahead">
                  <input type="text" class="input-small" ng-model="language"
                    typeahead="lang.name for lang in productLanguageCodeList | filter:$viewValue | limitTo:8"
                    typeahead-editable='false' typeahead-on-select="showLanguageCode($item)" />
                </span>
                <code>{{productItem.descriptiveDetail.language.languageRole}}</code>
                <code ng-init="productItem.descriptiveDetail.language.languageCode=''">{{productItem.descriptiveDetail.language.languageCode}}</code>
                <span>[{{'_more..._' | i18n}}]</span>
              </div>
            </div>

            <!-- SUBJECTS -->
            <div class="row"
              ng-repeat="subjectItem in productItem.descriptiveDetail.subject"

              ng-controller="SubjectCtrl" ng-include="'partials/subjects.html'">
            </div>

            <h4>{{'_Publishing_details_' | i18n}}</h4>

            <div class="row">
              <label class="span2">{{'_Publisher_' | i18n}}</label>
              <div class="span5">
                <select ng-model="productItem.publishingDetail.publisher.publishingRole" ng-options="key as value for (key, value) in publishingRoleList" class="input-medium" required/>
                  <option value="">{{'_...publishers_role_' | i18n}}</option>
                </select>
                <input type="text" ng-model="productItem.publishingDetail.publisher.publishingName" placeholder="{{'_Name_' | i18n}}" required/>
                <code>{{ productItem.publishingDetail.publisher.publishingRole }}</code>
              </div>
            </div>

            <div class="row">
              <label class="span2">{{'_Country_of_publication_' | i18n}}</label>
              <div class="span5" ng-controller="TypeaheadCtrl" id="countryTypeahead">
                <input type="text" ng-model="country"
                    typeahead="country.name for country in countryList | filter:$viewValue | limitTo:8"
                    typeahead-editable='false' class="input-medium" typeahead-on-select="showCountryCode($item)" required/>
                <code ng-init="productItem.publishingDetail.countryOfPublication=''">{{ productItem.publishingDetail.countryOfPublication }}</code>
              </div>
            </div>

            <div class="row">
              <label class="span2">{{'_Publishing_status_' | i18n}}</label>
              <div class="span5">
                <select ng-model="productItem.publishingDetail.publishingStatus" ng-options="key as value for (key, value) in publishingStatusList" required />
                  <option value="">{{'_Select_publishing_status_' | i18n}}</option>
                </select>
                <code>{{ productItem.publishingDetail.publishingStatus }}</code>
              </div>

            </div>

            <div class="row">
              <label class="span2">{{'_Publishing_date_' | i18n}}</label>
              <div class="span5">
                <select ng-model="productItem.publishingDetail.publishingDate.publishingDateRole" ng-options="key as value for (key, value) in publishingDateRoleList" required/>
                  <option value="">{{'_...status_' | i18n}}</option>
                </select>
                <code>{{ productItem.publishingDetail.publishingDate.publishingDateRole }}</code>

                <div ng-controller="DatepickerCtrl">
                  <div class="form-horizontal">
                      <input type="text" id="date-picker" class="input-small" datepicker-popup="yyyyMMdd"
                       ng-model="productItem.publishingDetail.publishingDate.date" is-open="opened" min="minDate" max="'2015-06-22'"
                       datepicker-options="dateOptions" date-disabled="disabled(date, mode)" ng-required="true" />
                      <button class="btn btn-small btn-danger" ng-click="clear2()">{{'_Clear_' | i18n}}</button>
                  </div>
                </div>
              </div>
            </div>

            <h4>{{'_Product_supply_' | i18n}}</h4>

            <div class="row">
              <label class="span2">{{'_Supplier_' | i18n}}</label>
              <div class="span5">
                <select id="supplier-role" ng-model="productItem.productSupply.supplyDetail.supplier.supplierRole" ng-options="key as value for (key, value) in supplierRoleList" required>
                  <option value="">{{'_Select_supplier_role_' | i18n}}</option>
                </select>
                <code>{{productItem.productSupply.supplyDetail.supplier.supplierRole}}</code>
                <input type="text" ng-model="productItem.productSupply.supplyDetail.supplier.supplierName" placeholder="{{'_Name_' | i18n}}" required/>
              </div>
            </div>

            <div class="row">
              <label class="span2">{{'_Product_availability_' | i18n}}</label>
              <div class="span5" ng-controller="TypeaheadCtrl" id="availabilityTypeahead">
                <input type="text" ng-model="productAvailability"
                    typeahead="availability.name for availability in productAvailabilityList | filter:$viewValue | limitTo:8"
                    typeahead-editable='false' typeahead-on-select="showAvailabilityCode($item)" required/>
                <code ng-init="productItem.productSupply.supplyDetail.productAvailability=''">
                  {{ productItem.productSupply.supplyDetail.productAvailability }}
                </code>
              </div>
            </div>

            <div class="row">
              <label class="span2"></label>
              <div class="span5" ng-init="priced=0">
                <label class="radio inline"><input type="radio" ng-model="priced" value=1
                  ng-change="productItem.productSupply.supplyDetail.unpricedItemType=null" />{{'_Priced_' | i18n}}
                </label >
                <label class="radio inline"><input type="radio" ng-model="priced" value=0
                  ng-change="productItem.productSupply.supplyDetail.price={}" />{{'_Unpriced_' | i18n}}
                </label>
              </div>
            </div>

            <div class="row" ng-hide="priced">
              <label class="span2">{{'_Unpriced_item_type_' | i18n}}</label>
              <div class="span5">
                <select ng-model="productItem.productSupply.supplyDetail.unpricedItemType" ng-options="key as value for (key, value) in unpricedCodeList">
                  <option value="">{{'_Select_unpriced_item_type_' | i18n}}</option>
                </select>
                <code>{{productItem.productSupply.supplyDetail.unpricedItemType}}</code>
              </div>
            </div>

            <div class="row" ng-show="priced">
              <label class="span2">{{'_Price_type_' | i18n}}</label>
              <div class="span5" ng-controller="TypeaheadCtrl" id="priceTypeTypeahead">
                <input type="text" ng-model="priceType"
                  typeahead="typ.name for typ in priceTypes | filter:$viewValue | limitTo:8"
                  typeahead-editable='false' required typeahead-on-select="showPriceTypeCode($item)" />
                <code ng-init="productItem.productSupply.supplyDetail.price.priceType=''">{{ productItem.productSupply.supplyDetail.price.priceType }}</code>
              </div>
            </div>

            <div class="row" ng-show="priced">
              <label class="span2">{{'_Price_amount_' | i18n}}</label>
              <div class="span5">
                <input type="text" name="priceAmount" class="input-small"
                 ng-pattern="/^(\d{1,5}(\.\d{0,3}){0,1})$/" ng-model="productItem.productSupply.supplyDetail.price.priceAmount"
                 maxlength="10" required/>
                <span ng-controller="TypeaheadCtrl" id="currencyTypeahead">
                  <input type="text"  class="input-small" ng-model="currencyCode"
                    typeahead="curr.name for curr in currencies | filter:$viewValue | limitTo:8"
                    typeahead-editable='false' placeholder="{{'_...currency_' | i18n}}" required
                    typeahead-on-select="showCurrencyCode($item)"/>
                  <code ng-init="productItem.productSupply.supplyDetail.price.currencyCode=''">
                    {{ productItem.productSupply.supplyDetail.price.currencyCode }}
                  </code>
                </span>
                  <small class="text-error" ng-show="productForm.priceAmount.$error.pattern">
                    {{'_Only_numbers_' | i18n}}
                  </small>

              </div>
            </div>

            <div class="row" ng-show="priced">
              <label class="span2">{{'_Price_code_type_' | i18n}}</label>
              <div class="span5">
                <select type="text" ng-model="productItem.productSupply.supplyDetail.price.priceCoded.priceCodeType"
                  ng-init="productItem.productSupply.supplyDetail.price.priceCoded.priceCodeType='02'" required/>
                  <option value="01" selected="selected">{{'_Proprietary_' | i18n}}</option>
                  <option value="02">{{'_Finnish_price_code_' | i18n}}</option>
                </select>
                <input type="text"
                  ng-model="productItem.productSupply.supplyDetail.price.priceCoded.priceCode"
                  placeholder="{{'_Price_code_' | i18n}}" required/>
                <code>{{productItem.productSupply.supplyDetail.price.priceCoded.priceCodeType}}</code>
              </div>
            </div>

              [ <a href="" ng-click="removeProduct(productItem)">X</a> ]
            </div>

            <a href="" class="btn" ng-click="addProduct()">{{'_add_more_products_' | i18n}}</a>
            <br />

            <button ng-click="reset()" ng-disabled="isUnchanged(mgsessage)">{{'_Reset_' | i18n}}</button>
         	  <button ng-click="send()" ng-disabled="messageForm.$invalid">{{'_Send_' | i18n}}</button>
          </fieldset>
        </form>
      </div>
    </div>

    <div class="span4">
    	<h3>{{'_ONIX_msg_' | i18n}}</h3>
        <div ng-controller="AlertCtrl">
          <alert ng-repeat="alert in alerts | reverse" type="alert.type" close="closeAlert($index)"><span ng-bind-html-unsafe="alert.msg"></span></alert>
        </div>
    		<pre>form = &lt;?xml version="1.0" encoding="UTF-8" ?&gt;
&lt;ONIXMessage xmlns="http://ns.editeur.org/onix/3.0/reference" release="3.0"&gt;
{{ message | onixize }}</pre>
        Debug: <input type="checkbox" ng-model="debug" />
        <pre ng-show="debug">{{times}}

          {{ message | json }}</pre>
    </div>

  </div>
  <?php
    } else { ?>
      <div ng-controller="ModalCtrl">
        <button class="btn" style="position: absolute; top: 8px; right: 10px" ng-click="logout()">{{'_Logout_' | i18n}}</button>
      </div>
      <div class="row">
        <alert class="alert-danger span5" data-i18n="_Naughty!!!_"></alert>
      </div>
  <?php
    }
  } else { ?>
      <div ng-controller="ModalCtrl">
        <button class="btn" style="position: absolute; top: 8px; right: 10px" ng-click="open()">{{'_Login_' | i18n}}</button>
      </div>
      <div class="row">
        <div class="span5">
          <alert class="alert-info" data-i18n="_Wanna_login_"></alert>
        </div>
      </div>
  <?php } ?>