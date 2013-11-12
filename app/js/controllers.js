'use strict';

/* Controllers */

angular.module('myApp.controllers', ['ui.bootstrap']).
  controller('MainCtrl', ['$scope', '$location', 'localize',
      function($scope, $location, localize) {

    $scope.getClass = function(path) {
      if ($location.path().substr(0, path.length) == path) {
        return "active";
      } else {
        return "";
      }
    };

    $scope.setFinnishLanguage = function() {
        localize.setLanguage('default');
    };

    $scope.setEnglishLanguage = function() {
        localize.setLanguage('en-UK');
    };

  }])

  .controller('MyCtrl2', [function() {

  }])

  .controller('ModalCtrl', ['$scope', '$modal', '$log', '$http',
      function($scope, $modal, $log, $http) {
    $scope.password = '';

    $scope.open = function () {

      var modalInstance = $modal.open({
        templateUrl: 'partials/loginModal.html',
        controller: 'ModalInstanceCtrl'
      });

      modalInstance.result.then(function (enteredText) {
        // try log in the user
        $http.post('sessions.php?login=1', {password: enteredText})
          .success(function(data) {
            $log.info(data);
            if (data.status == 'notLoggedIn') {
              var modalFailedPass = $modal.open({
                templateUrl: 'partials/failedLogin.html',
                controller: 'ModalInstanceCtrl'
              });
            } else {
              // Reload the page
              location.reload();
            }
          })
          .error(function(data){
            alert("Couldn't log you in!");
          });
        $scope.password = enteredText;
      }, function () {
        $log.info('Modal dismissed at: ' + new Date());
      });
    };

    $scope.logout = function() {
      $http.get('sessions.php?logout=1')
        .success(function(data) {
          $log.info(data);
          // Reload the page
          location.reload();
          $log.info('Reload');
        })
        // shouldn't happen
        .error(function(data){
          alert("Couldn't log you out!");
        });
    };
  }])

  .controller('ModalInstanceCtrl', ['$scope', '$modalInstance', function($scope, $modalInstance) {
    $scope.input = {};

    $scope.ok = function() {
      $modalInstance.close($scope.input.pass);
    };

    $scope.cancel = function() {
      $modalInstance.dismiss('cancel');
    };
  }])

  .controller('MessageCtrl', ['$scope','$http', 'localize', '$filter',
      function($scope, $http, localize, $filter) {

    // TODO: move to configurational file
    var jonix_proxy = "./send.php";
    var lang = localize.language;
    var availableLanguages = ['default', 'en-UK'];
    // is lang in array?
    if (availableLanguages.indexOf(localize.language) === -1) {
      lang = 'default';
    }

    $scope.times = {
      sentDate: {},
      sentTime: {}
    };

    $scope.master = {
      header: {},
      products: [
        {
          descriptiveDetail: {
            subjects: [
              {
                subjectSchemeIdentifier:'',
                subjectHeadingText:''
              }
            ]
          }
        }
      ]
    };

    // Get the lists for the forms
  	$scope.productNotificationTypeList = {};
  	$http.get('assets/lists/selects_'+lang+'.json').success(function(data){
  		$scope.productNotificationTypeList  = data.list1;
      $scope.productCompositionList       = data.list2;
      $scope.productFormList              = data.list7;
      $scope.productIdTypeList            = data.list5;
      $scope.productTitleTypeList         = data.list15;
      $scope.productLanguageRoleList      = data.list22;
      $scope.nameCodeTypeList             = data.list44;
      $scope.publishingRoleList           = data.list45;
      $scope.unpricedCodeList             = data.list57;
      $scope.publishingStatusList         = data.list64;
      $scope.supplierRoleList             = data.list93;
      $scope.productTitleElementLevelList = data.list149;
      $scope.publishingDateRoleList       = data.list163;
      //Save the data for later use
      $scope.lists = data;
  	});

    $scope.senderIDValuePattern = (function() {
      // http://stackoverflow.com/questions/18900308/angularjs-dynamic-ng-pattern-validation
      var regexp = /^(.*)$/;

      return {
        test: function(value) {
          switch ($scope.message.header.sender.senderIDType)
          {
            // Y-tunnus
            case '15':
              regexp = /^(\d{7}-\d)$/;
              break;
            // ISNI
            case '16':
              regexp = /^(\d{16})$/;
              break;
            default:
              regexp = /^(.*)$/;
              break;
          };

          return regexp.test(value);
        }
      };
    })();

    $scope.setupDates = function() {
      var now = $filter('date')(new Date(), 'yyyyMMdd');
      $scope.message.header.sentDateTime = now;
    }

    $scope.updateSentDateTime = function() {
      // If the time value has not been set yet
      if (!angular.isDefined($scope.times.sentTime)) {
        $scope.message.header.sentTime = '';
      }
      // When the time selector is shown, update SentDateTime
      if ($scope.showTime) {
        var sentDate = $filter('date')($scope.times.sentDate, 'yyyyMMdd');
        var sentTime = $filter('date')($scope.times.sentTime, 'HHmm');
      } else {
        var sentDate = $filter('date')($scope.times.sentDate, 'yyyyMMdd');
        var sentTime = '';
      }

      $scope.message.header.sentDateTime = sentDate + sentTime;
    }

    // Add a product
   	$scope.addProduct = function() {
  		$scope.message.products.push(
        {
          descriptiveDetail: {
            subjects: [
              {
                subjectSchemeIdentifier:'',
                subjectHeadingText:''
              }
            ]
          }
        }
      );
  	};

    // Remove a product
  	$scope.removeProduct = function(product) {
  		var products = $scope.message.products;
  		for (var i = 0, ii = products.length; i < ii; i++) {
  			if (product === products[i]) {
  				products.splice(i, 1);
  			}
  		}
  	};

  	$scope.update = function(message) {
  		$scope.master = angular.copy(message);
  	};

  	$scope.reset = function () {
  		$scope.message = angular.copy($scope.master);
  	};

    // Send the filled in form
    // Get the info what is the response from the service
    $scope.send = function () {
      $http.post(jonix_proxy, $scope.message,
       {
        'Content-Type':'application/xml'
       }
      ).success(function(data,status) {
        $scope.$broadcast('answer', [data, status]);
      })
      .error(function(data, status){
        $scope.$broadcast('answer', [data, status]);
        alert('Proxy is down!');
      });
    }

    // Changes the available product forms based on the ID type
    $scope.changeProductForm = function(item) {

      switch(item)
      {
        case "02":
        case "15":
          $scope.productFormList = {
                "00":"Määrittelemätön",
                "BA":"Kirja",
                "BB":"Kovakantinen kirja",
                "BC":"Pehmeäkantinen kirja",
                "BD":"Irtolehtiä, irtolehtijulkaisu",
                "BE":"Kierreselkä",
                "BF":"Lehtivihko, moniste",
                "BG":"Leather / fine binding",
                "BH":"Pahvisivuinen kirja",
                "BI":"Kangaskirja",
                "BJ":"Bath book",
                "BK":"Poikkeavan muotoinen kirja",
                "BL":"Slide bound",
                "BM":"Big book",
                "BN":"Part-work (fascículo)",
                "BO":"Haitarikirja, 'Leporello'",
                "BP":"'Kylpykirja'",
                "BZ":"Jokin muu kirjan muoto",
                "CA":"Kartta",
                "CB":"Sheet map, folded",
                "CC":"Sheet map, flat",
                "CD":"Sheet map, rolled",
                "CE":"Globe",
                "CZ":"Other cartographic"
          };
          break;
        case "02":
          break;
        default:
          $scope.productFormList = $scope.lists.list7;
          break;
      }
    };

    $scope.reset();
  }])

  .controller('SubjectCtrl', ['$scope', function($scope) {

    // Add more subject field to the form
    $scope.addSubject = function(productIndex) {
      $scope.product.descriptiveDetail.subjects.push({});
    };

    // Remove subjects
    $scope.removeSubject =  function(i) {
      $scope.product.descriptiveDetail.subjects.splice(i, 1);
    }

  }])

  .controller('DatepickerCtrl', ['$scope', '$timeout', function($scope, $timeout) {
  	 $scope.today = function() {
  	 	$scope.times.sentDate = new Date();
  	 };
  	 $scope.today();

     $scope.showWeeks = false;

  	 $scope.clear = function () {
  	    $scope.times.sentDate = null;
  	 };

     $scope.clear2 = function() {
      $scope.product.publishingDetail.publishingDate.date = null;
     }

  	 $scope.open = function() {
  	    $timeout(function() {
  	      $scope.opened = true;
  	    });
  	 };

  	 $scope.dateOptions = {
  	 	'year-format': "'yy'",
  	    'starting-day': 1
  	 };
  }])

  .controller('TimepickerCtrl', ['$scope', function($scope) {
  	$scope.times.sentTime = new Date();
  }])

  .controller('TypeaheadCtrl', ['$scope','$http','localize',
      function($scope, $http, localize) {
    $scope.selected = undefined;

    var lang = localize.language;
    var availableLanguages = ['default', 'en-UK'];
    // is lang in array?
    if (availableLanguages.indexOf(localize.language) === -1) {
      lang = 'default';
    }

    $http.get('assets/lists/typeheads_'+lang+'.json').success(function(data){
      $scope.subjectSchemeIdentifiers= data.list27;
      $scope.priceTypes              = data.list58;
      $scope.productAvailabilityList = data.list65;
      $scope.productLanguageCodeList = data.list74;
      $scope.countryList             = data.list91;
      $scope.currencies              = data.list96;
    });

    $scope.getKeywordsAjax = function(query, schemeIdentifier){
      if (query.length > 1) {

        //TODO: the language code should be sent over
        switch (schemeIdentifier) {
          case 64:
            return $http.get('./query.php?query='+query+'*&lang=fi&schid=64')
              .then(function(response){
                return limitToFilter(response.data, 15);
              });
            break;
          case 86:
            return $http.get('./query.php?query='+query+'*&lang=fi&schid=86')
              .then(function(response){
                return response.data.geonames;
              });
            break;
          default:
            break;
        };

      }
    };

    $scope.availabilityCodeFor = function(item) {
      $scope.productAvailability = item.code;
    };

    var limitToFilter = function(data, limit) {
      return data.results.splice(0,limit);
    };

    $scope.showSubjectSchemeIdentifier = function(data) {
      $scope.subject.subjectSchemeIdentifier = data.code;
    };

    $scope.showSubjectCode = function(data, type) {
      // Show links for the different subjects
      switch (type) {
        case "YSA":
          $scope.subject.subjCode.url = "http://www.yso.fi/onto/ysa/" + data.localname;

          $scope.subject.subjectCode = data.localname;
          break;
        case "Geonames":
          $scope.subject.subjCode.url = "http://geonames.org/" + data.geonameId;

          $scope.subject.subjectCode = data.geonameId;
          break;
      }
    };

    $scope.showLanguageCode = function(data) {
      $scope.product.descriptiveDetail.language.languageCode = data.code;
    };

    $scope.showCountryCode = function(data) {
      $scope.product.publishingDetail.countryOfPublication = data.code;
    };

    $scope.showAvailabilityCode = function(data) {
      $scope.product.productSupply.supplyDetail.productAvailability = data.code;
    }

    $scope.showPriceTypeCode = function(data) {
      $scope.product.productSupply.supplyDetail.price.priceType = data.code;
    }

    $scope.showCurrencyCode = function(data) {
      $scope.product.productSupply.supplyDetail.price.currencyCode = data.code;
    }

  }])

  .controller('AlertCtrl', ['$scope','$filter', function($scope, $filter) {
    $scope.alerts = [
    ];

    $scope.$on('answer', function(answer, data) {

      var now = $filter('date')(new Date(), 'dd MMMM yyyy h:mm:ss');

      var alert = {type: 'success', msg: 'I guess everything went fine' +
        '<br/><small>at ' + now + '</small>'};
      // types: success, info, warning, error
      // Connection with proxy (send.php) was succesful
      // TODO: prepare for all the HTTP codes
      if(data[1] == 200) {
        if (data[0].http_code == 406) {
          alert.type = "warning";
          alert.msg = '<strong>Backend responded:</strong><br/>' + data[0].result +
            '<br/>at <small>' + now + '</small>';
        }
      } else if (data[1] == 405) {
        alert.type = "error";
        alert.msg = "Failed! No connection!";
      } else {
        alert.type = "error";
        alert.msg = "Failed! Something else...";
      }
      $scope.alerts.push(alert);
    });

    $scope.closeAlert = function(index) {
      $scope.alerts.splice(index, 1);
    };
  }]);