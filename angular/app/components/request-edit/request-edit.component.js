class RequestEditController{
    constructor($rootScope,$stateParams, $state, API,$scope,$http, $uibModal, $log,$compile, DTOptionsBuilder, DTColumnBuilder) {
        'ngInject';
        this.$scope = $scope
        this.$http = $http
        this.alerts = []
        this.API = API
        this.$state = $state
        this.$uibModal = $uibModal
        this.$log = $log
        this.formSubmitted = false

        if ($stateParams.alerts) {
            this.alerts.push($stateParams.alerts)
        }
        $http.get('/api/common/sources').success(function (data, status, headers, config) {
            var list = []
            angular.forEach(data.response, function (value) {
                list.push({id: value.id, name: value.source_name})
            })
            $scope.vm.systemSources = list
        })
        $http.get('/api/common/bidstatus').success(function (data, status, headers, config) {
            var list = []
            angular.forEach(data.response, function (value) {
                list.push({id: value.id, name: value.bid_status_name})
            })
            $scope.vm.systemBidStatus = list
        })
        $http.get('/api/common/estimator').success(function (data, status, headers, config) {
            var list = []
            angular.forEach(data.response, function (value) {
                list.push({id: value.id, name: value.name})
            })
            $scope.vm.systemEstimators = list
        })
        let requestId = $stateParams.requestId
        $scope.requestId = $stateParams.requestId
        var formData = {
            'requestId'  : requestId
        }
        $scope.vm.comment ={
            'property_comment_id' : ""
        }

        $http.post('/api/request/getrequest',formData).success(function(data,status,headers, config) {
            $scope.vm.requester_name = data.request.requester_name
            $scope.vm.requester_phone = data.request.requester_phone
            $scope.vm.requester_fax = data.request.requester_fax
            $scope.vm.scope = data.request.scope
            $scope.vm.details = data.request.details
            $scope.vm.source_id = data.request.source_id.toString()
            $scope.vm.bid_statuses_id = data.request.bid_statuses_id.toString()
            $scope.vm.estimator_id = data.request.estimator_id.toString()

            if(data.response.property.length  != 0){
                $scope.property_name = data.response.property.property_name
                $scope.property_street = data.response.property.property_street
                $scope.property_city = data.response.property.property_city
                $scope.property_state = data.response.property.property_state
                $scope.property_zip =   data.response.property.property_zip

            }else{
                $scope.property_name = ""
                $scope.property_street = ""
                $scope.property_city = ""
                $scope.property_state = ""
                $scope.property_zip =   ""
            }
            $scope.property_phone = data.response.property_phone
            $scope.property_fax = data.response.property_fax
            $scope.contact = data.response.contact_name
            if(data.response.property_company_list.length != 0){
                $scope.management_company = data.response.property_company_list.management_company
                $scope.management_company_street = data.response.property_company_list.management_company_street
                $scope.management_company_city = data.response.property_company_list.management_company_city
                $scope.management_company_state = data.response.property_company_list.management_company_state
                $scope.management_company_zip = data.response.property_company_list.management_company_zip
            }else{
                $scope.management_company = ""
                $scope.management_company_street = ""
                $scope.management_company_city = ""
                $scope.management_company_state = ""
                $scope.management_company_zip = ""
            }

            $scope.management_company_phone = data.response.company_phone
            $scope.management_company_fax = data.response.company_phone_fax
            $scope.assign_request_id = data.request.id

            $scope.assign_property_id = $rootScope.assign_property_id = data.property_list.property_id
            $scope.assign_contact_id = $rootScope.assign_contact_id =  data.property_list.property_contact_id
            $scope.assign_contact_phone_id = $rootScope.assign_contact_phone_id = data.property_list.property_contact_phone_id
            $scope.assign_phone_id = $rootScope.assign_phone_id  = data.property_list.property_phone_id
            $scope.assign_company_id = $rootScope.assign_company_id  = data.property_list.property_company_id
            $scope.assign_company_phone_id = $rootScope.assign_company_phone_id  = data.property_list.property_company_phone_id
            $scope.assign_company_contact_id = $rootScope.assign_company_contact_id  = data.property_list.property_company_contact_id

            $rootScope.$broadcast("initial-property-value")
            if (!$scope.vm.comment.property_comment_id) {
                if(data.comment.length !=0) {
                    $scope.vm.comment.property_comment_id =  data.comment[0].id
                }
            }
            $scope.showAddEditRequestCommentList(data)
            $scope.showAddEditRequestFileList()

        })

        $scope.$on('update-property-value', function() {
            $scope.assign_property_id = $rootScope.assign_property_id
            $scope.assign_contact_id = $rootScope.assign_contact_id
            $scope.assign_contact_phone_id = $rootScope.assign_contact_phone_id
            $scope.assign_phone_id = $rootScope.assign_phone_id
            $scope.assign_company_id = $rootScope.assign_company_id
            $scope.assign_company_phone_id = $rootScope.assign_company_phone_id
            $scope.assign_company_contact_id = $rootScope.assign_company_contact_id

            $scope.property_name = $rootScope.property_name
            $scope.property_street = $rootScope.property_street
            $scope.property_city = $rootScope.property_city
            $scope.property_state = $rootScope.property_state
            $scope.property_zip = $rootScope.property_zip
            $scope.property_phone = $rootScope.property_phone
            $scope.property_fax = $rootScope.property_fax
            $scope.contact = $rootScope.contact
            $scope.management_company = $rootScope.management_company
            $scope.management_company_street = $rootScope.management_company_street
            $scope.management_company_city = $rootScope.management_company_city
            $scope.management_company_state = $rootScope.management_company_state
            $scope.management_company_zip = $rootScope.management_company_zip
            $scope.management_company_phone = $rootScope.management_company_phone
            $scope.management_company_fax = $rootScope.management_company_fax
        })

        $scope.vm.request_comment_add_edit = ""

        $scope.onClickRequestAddComment = function(){
            $scope.vm.formSubmitted = false
            $scope.request_comment_form_show = true
        }
        $scope.onClickRequestCommentCancel = function(){
            angular.forEach(Object.keys($scope.vm.comment), function(key){
                if (key == 'property_comment_id') return
                $scope.vm.comment[key] = ""
            })
            $scope.vm.request_comment_add_edit = ""
            $scope.request_comment_form_show = false
            this.requestCommentAddForm.$setPristine()
            this.requestCommentAddForm.$setUntouched()
            this.formSubmitted = false
        }

        $scope.createBid = function(){
            $scope.formData = {
                'request_id': $scope.requestId,
            }
            $http({
                method  : 'POST',
                url     : '/api/request/requestbid',
                data    : $.param($scope.formData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
            .success(function(data) {
                var redirectRequestId = data.response.id
                $state.go("app.bidsadd",{"requestId": redirectRequestId})
            })
        }
        this.requestCommentSave = function(isValid){
            if (isValid) {
                $scope.formData = {
                    'comment': this.comment.comment,
                    'request_id': this.$scope.assign_request_id,
                    'request_comment_id' : this.request_comment_add_edit
                }
                $http({
                    method  : 'POST',
                    url     : '/api/request/requestcomment',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickRequestCommentCancel()
                            if (!$scope.vm.comment.property_comment_id) {
                                $scope.vm.comment = {
                                    property_comment_id :data.comment[0].id
                                }
                            }
                            $scope.showAddEditRequestCommentList(data)
                        }
                    })
            }else{
                this.formSubmitted = true
            }
        }
        $scope.onClickRequestEditComment = function(){
            if($scope.vm.comment.property_comment_id != "") {
                $scope.formData = {
                    'request_comment_id' : $scope.vm.comment.property_comment_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/request/requesteditcommentdata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.vm.request_comment_add_edit = data.request_comment.id
                            $scope.vm.comment.comment = data.request_comment.comment
                            $scope.onClickRequestAddComment()
                        }
                    })
            }else{
                alert("Please select one property comment")
            }
        }
        $scope.showAddEditRequestFileList = function(){
            let dataSet = []
            $scope.vm.dtOptions11 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.vm.dtColumns11 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsRequestCommentHtml),
                DTColumnBuilder.newColumn('file_type').withTitle('File Type'),
                DTColumnBuilder.newColumn('description').withTitle('Description'),
                DTColumnBuilder.newColumn('file_name').withTitle('File Name'),
            ]
            $scope.vm.displayTable11  = true
        }
        $scope.ShowAddEditRequestComment = function(){
            let dataSet = $scope.request_comments
            $scope.vm.dtOptions10 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.vm.dtColumns10 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsRequestCommentHtml),
                DTColumnBuilder.newColumn('comment').withTitle('Comment'),
                DTColumnBuilder.newColumn('creator_id').withTitle('Creator'),
            ]
            $scope.vm.displayTable10  = true
        }
        $scope.showAddEditRequestCommentList = function(data){
            let list = []
            angular.forEach(data.comment, function(value){
                list.push({id: value.id, comment: value.comment,creator_id: value.creator_id})
            })
            $scope.request_comments = list
            $scope.ShowAddEditRequestComment()
        }

        let actionsRequestCommentHtml = (data) =>{
            return `
                <input type="radio"  name="property_comment_id" value="${data.id}" ng-model="vm.comment.property_comment_id" ng-select = "{data.id} == {$scope.vm.comment.property_comment_id}">
                `
        }
        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }
    }
    modalOpen(size) {
        let $uibModal = this.$uibModal
        let $scope = this.$scope
        let $log = this.$log
        let items = this.items


        var modalInstance = $uibModal.open({
            animation: this.animationsEnabled,
            templateUrl: 'myModalContent.html',
            controller: this.modalcontroller,
            controllerAs: 'mvm',
            size: size,
        })

    }

    /****Modal Function ****/
    modalcontroller($rootScope,$scope, $http,$uibModalInstance,$compile, DTOptionsBuilder, DTColumnBuilder,$auth) {
        'ngInject'
        this.$http = $http
        this.formSubmitted = false
        this.errors = []
        this.$auth = $auth
        $scope.search_property_form = true
        $scope.new_property_save = true
        $http.get('/api/common/propertytype').success(function(data,status,headers, config){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, name : value.name})
            })
            $scope.mvm.systemPropertyType = list
        })
        $http.get('/api/common/mailtotype').success(function(data,status,headers, config){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, name : value.name})
            })
            $scope.mvm.systemMailToTypes = list
        })
        $http.get('/api/common/phonetype').success(function(data,status,headers, config){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, name : value.name})
            })
            $scope.mvm.systemPhoneTypes = list
        })
        $http.get('/api/common/contacttype').success(function(data,status,headers, config){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, name : value.name})
            })
            $scope.mvm.systemContactTypes = list
        })
        $scope.$on('initial-property-value', function() {
            $scope.assign_property_id = $rootScope.assign_property_id
            $scope.assign_contact_id = $rootScope.assign_contact_id
            $scope.assign_contact_phone_id = $rootScope.assign_contact_phone_id
            $scope.assign_phone_id = $rootScope.assign_phone_id
            $scope.assign_company_id = $rootScope.assign_company_id
            $scope.assign_company_phone_id = $rootScope.assign_company_phone_id
            $scope.assign_company_contact_id = $rootScope.assign_company_contact_id
        })

        function initNew() {
            $scope.mvm.new ={
                property: {

                },
                phone : {
                    property_phone_id : $scope.assign_phone_id
                },
                contact:{
                    property_contact_id : $scope.assign_contact_id
                },
                comment:{
                    property_comment_id : ""
                },
                management:{
                    property_company_id: $scope.assign_company_id
                },
                assignmanagement:{

                },
                companyphone:{
                    property_phone_id: $scope.assign_company_phone_id
                },
                companycontact:{
                    property_contact_id : $scope.assign_company_contact_id
                }
            }
            $scope.mvm.search = {

            }
            $scope.mvm.property_show_id =$scope.assign_property_id
            $scope.mvm.property_id = ""
            $scope.mvm.property_phone_add_edit = ""
            $scope.mvm.property_contact_add_edit = ""
            $scope.mvm.property_comment_add_edit = ""
            $scope.mvm.property_company_add_edit = ""
            $scope.mvm.property_company_phone_add_edit = ""
            $scope.mvm.property_company_contact_add_edit = ""
        }



        /*******Get Properties*******/
        $scope.onGetPropertiesBody = function(data){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, property_name : value.property_name, property_street : value.property_street, property_city : value.property_city, property_state:value.property_state, property_zip: value.property_zip})
            })

            let dataSet =  list
            $scope.mvm.displayTable6 = true
            $scope.mvm.dtOptions6 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns6 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyHtml),
                DTColumnBuilder.newColumn('property_name').withTitle('Property Name'),
                DTColumnBuilder.newColumn('property_street').withTitle('Address'),
                DTColumnBuilder.newColumn('property_city').withTitle('City'),
                DTColumnBuilder.newColumn('property_state').withTitle('State'),
                DTColumnBuilder.newColumn('property_zip').withTitle('Zip'),
            ]
            if(list.length != 0){
                if (!$scope.mvm.property_show_id){
                    $scope.mvm.property_show_id = list[0].id
                }
            }
        }
        $scope.onGetProperties = function() {
            $http.get('/api/property/properties').success(function(data,status,headers, config){
                $scope.onGetPropertiesBody(data)
            })
        }
        let actionsPropertyHtml = (data) =>{
            return `
                <input type="radio"  name="property_id" value="${data.id}" ng-model="mvm.property_show_id" ng-select = "{data.id} == {$scope.mvm.property_show_id}">
                `
        }
        initNew()
        $scope.onGetProperties()
        $scope.onAssignSelectedProperty = function(){
            $scope.formData ={
                'property_id' : $scope.mvm.property_show_id,
                'contact_id' : $scope.mvm.new.contact.property_contact_id,
                'contact_phone_id' : "",
                'phone_id' :  $scope.mvm.new.phone.property_phone_id,
                'company_id' : $scope.mvm.new.management.property_company_id,
                'company_phone_id' : $scope.mvm.new.companyphone.property_phone_id,
                'company_contact_id' :$scope.mvm.new.companyphone.property_contact_id,
            }
            $http({
                method  : 'POST',
                url     : '/api/property/assignproperty',
                data    : $.param($scope.formData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
                .success(function(data) {
                    $rootScope.assign_property_id = $scope.mvm.property_show_id
                    $rootScope.assign_contact_id = $scope.mvm.new.contact.property_contact_id
                    $rootScope.assign_contact_phone_id = ""
                    $rootScope.assign_phone_id = $scope.mvm.new.phone.property_phone_id
                    $rootScope.assign_company_id = $scope.mvm.new.management.property_company_id
                    $rootScope.assign_company_phone_id = $scope.mvm.new.companyphone.property_phone_id
                    $rootScope.assign_company_contact_id = $scope.mvm.new.companyphone.property_contact_id
                    $rootScope.property_name = data.property.property_name
                    $rootScope.property_street = data.property.property_street
                    $rootScope.property_city = data.property.property_city
                    $rootScope.property_state = data.property.property_state
                    $rootScope.property_zip = data.property.property_zip
                    $rootScope.property_phone = data.result.phone
                    $rootScope.property_fax = data.result.phone_fax
                    $rootScope.contact = data.result.contact_name
                    if(data.result.property_company_list.length != 0){
                        $rootScope.management_company = data.result.property_company_list.management_company
                        $rootScope.management_company_street = data.result.property_company_list.management_company_street
                        $rootScope.management_company_city = data.result.property_company_list.management_company_city
                        $rootScope.management_company_state = data.result.property_company_list.management_company_state
                        $rootScope.management_company_zip = data.result.property_company_list.management_company_zip
                    }else{
                        $rootScope.management_company = ""
                        $rootScope.management_company_street = ""
                        $rootScope.management_company_city = ""
                        $rootScope.management_company_state = ""
                        $rootScope.management_company_zip = ""
                    }
                    $rootScope.management_company_phone = data.result.company_phone
                    $rootScope.management_company_fax = data.result.company_phone_fax
                    $rootScope.$broadcast("update-property-value")
                    $uibModalInstance.dismiss('cancel')

                })
        }
        this.searchProperty = function(){
            $scope.formData = {
                'property_name': this.search.property_name,
                'property_street': this.search.property_street,
                'property_city': this.search.property_city,
                'property_state':this.search.property_state,

            }
            $http({
                method  : 'POST',
                url     : '/api/property/searchproperty',
                data    : $.param($scope.formData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
                .success(function(data) {
                    $scope.onGetPropertiesBody(data)
                })
        }
        $scope.onShowPropertyDetails = function(){
            $scope.mvm.property_id = $scope.mvm.property_show_id
            if($scope.mvm.property_show_id != ""){
                $scope.formData = {
                    'property_id': $scope.mvm.property_show_id,
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyshow',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        $scope.propery_add_edit_show_form= true

                        if (!$scope.mvm.new.phone.property_phone_id) {
                            if (typeof data.phone != "undefined") {
                                if(data.phone.length !=0){
                                    $scope.mvm.new.phone = {
                                        property_phone_id :data.phone[0].id
                                    }
                                }
                            }

                        }
                        if (!$scope.mvm.new.contact.property_contact_id) {
                            if(data.contact.length !=0){
                                $scope.mvm.new.contact = {
                                    property_contact_id :data.contact[0].id
                                }
                            }
                        }
                        if (!$scope.mvm.new.comment.property_comment_id) {
                            if(data.comment.length !=0) {
                                $scope.mvm.new.comment = {
                                    property_comment_id: data.comment[0].id
                                }
                            }
                        }
                        if (!$scope.mvm.new.management.property_company_id) {
                            if(data.company.length !=0) {
                                $scope.mvm.new.management = {
                                    property_company_id: data.company[0].id
                                }
                            }
                        }
                        if(data.company.length != 0){
                            $scope.managementcompanyempty = false
                        }else{
                            $scope.managementcompanyempty = true
                        }

                        $scope.showAddEditPropertyPhoneList(data)
                        $scope.showAddEditPropertyContactList(data)
                        $scope.showAddEditPropertyCommentList(data)
                        $scope.showAddEditPropertyCompanyList(data)
                    })
            }else{
                alert("Please select property")
            }
        }
        $scope.onAssignPropertyEdit = function() {
            if($scope.mvm.property_show_id != "") {
                $scope.formData = {
                    'property_id': $scope.mvm.property_show_id,
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/getedtiproperty',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        $scope.mvm.property_id = data.response.id
                        $scope.mvm.new.property.property_name = data.response.property_name
                        $scope.mvm.new.property.property_street = data.response.property_street
                        $scope.mvm.new.property.property_city = data.response.property_city
                        $scope.mvm.new.property.property_state = data.response.property_state
                        $scope.mvm.new.property.property_zip = data.response.property_zip
                        $scope.mvm.new.property.property_type = data.response.property_type_id
                        $scope.mvm.new.property.mail_to_type_id = data.response.mail_to_type_id

                        $scope.onAssignPropertyAddNew()
                    })
            }
        }


        this.cancel = () => {
            $uibModalInstance.dismiss('cancel')
        }
        $scope.onAssignPropertyAddNew = function(){
            $scope.mvm.formSubmitted = false
            $scope.search_property_form = false
            $scope.new_property_save = true
            $scope.new_property_form = true
            $scope.propery_add_edit_show_form = false
            $scope.property_phone_form_show = false
            $scope.assign_company = false
        }
        $scope.onCancelNewProperty = function(){
            $scope.search_property_form = true
            $scope.new_property_save = true
            $scope.new_property_form = false
            $scope.propery_add_edit_show_form = false
            $scope.property_phone_form_show = false
            $scope.assign_company = false
            initNew()
            $scope.mvm.property_id =""
            this.assignPropertyAddForm.$setPristine()
            this.assignPropertyAddForm.$setUntouched()
            this.formSubmitted = false
            $scope.onGetProperties()
        }
        /*** phone add and edit  ****/
        $scope.onClickAddPhone = function () {
            $scope.mvm.formSubmitted = false
            $scope.request_comment_form_show = true
        }
        $scope.onClickPhoneNumberCancel = function(){
            angular.forEach(Object.keys($scope.mvm.new.phone), function(key){
                if (key == 'property_phone_id') return
                $scope.mvm.new.phone[key] = ""
            })
            $scope.mvm.property_phone_add_edit = ""
            $scope.property_phone_form_show = false
            this.propertyPhoneAddForm.$setPristine()
            this.propertyPhoneAddForm.$setUntouched()
            this.formSubmitted = false
        }

        /***Contact Add and Edit *****/

        $scope.onClickPropertyAddContact = function(){
            $scope.mvm.formSubmitted = false
            $scope.property_contact_form_show = true
        }
        $scope.onClickPropertyContactCancel = function(){
            angular.forEach(Object.keys($scope.mvm.new.contact), function(key){
                if (key == 'property_contact_id') return
                $scope.mvm.new.contact[key] = ""
            })
            $scope.mvm.property_contact_add_edit = ""
            $scope.property_contact_form_show = false
            this.propertyContactAddForm.$setPristine()
            this.propertyContactAddForm.$setUntouched()
            this.formSubmitted = false
        }
        /****Comment Add and Edit ****/
        $scope.onClickPropertyAddComment = function(){
            $scope.mvm.formSubmitted = false
            $scope.property_comment_form_show = true
        }
        $scope.onClickPropertyCommentCancel = function(){
            angular.forEach(Object.keys($scope.mvm.new.comment), function(key){
                if (key == 'property_comment_id') return
                $scope.mvm.new.comment[key] = ""
            })
            $scope.mvm.property_comment_add_edit = ""
            $scope.property_comment_form_show = false
            this.propertyCommentAddForm.$setPristine()
            this.propertyCommentAddForm.$setUntouched()
            this.formSubmitted = false
        }

        /****Management Company Add and Edit *****/
        $scope.onClickPropertyAddManagementCompany = function(){
            $scope.mvm.formSubmitted = false
            $scope.property_management_company_form_show = true
        }
        $scope.onClickPropertyCompanyCancel = function(){
            angular.forEach(Object.keys($scope.mvm.new.management), function(key){
                if (key == 'property_company_id') return
                $scope.mvm.new.management[key] = ""
            })
            $scope.mvm.property_company_add_edit = ""
            $scope.property_management_company_form_show = false
            this.propertyCompanyManagementAddForm.$setPristine()
            this.propertyCompanyManagementAddForm.$setUntouched()
            this.formSubmitted = false
        }

        /*** company phone add and edit  ****/
        $scope.onClickAddCompanyPhone = function () {
            $scope.mvm.formSubmitted = false
            $scope.property_company_phone_form_show = true
        }
        $scope.onClickCompanyPhoneNumberCancel = function(){
            angular.forEach(Object.keys($scope.mvm.new.companyphone), function(key){
                if (key == 'property_phone_id') return
                $scope.mvm.new.companyphone[key] = ""
            })
            $scope.mvm.property_company_phone_add_edit  =""
            $scope.property_company_phone_form_show = false
            this.propertyCompanyPhoneAddForm.$setPristine()
            this.propertyCompanyPhoneAddForm.$setUntouched()
            this.formSubmitted = false
        }

        /****company contact Add and Edit ****/
        $scope.onClickPropertyAddCompanyContact = function(){
            $scope.mvm.formSubmitted = false
            $scope.property_company_contact_form_show = true
        }
        $scope.onClickPropertyCompanyContactCancel = function(){
            angular.forEach(Object.keys($scope.mvm.new.companycontact), function(key){
                if (key == 'property_contact_id') return
                $scope.mvm.new.companycontact[key] = ""
            })
            $scope.property_company_contact_add_edit = ""
            $scope.property_company_contact_form_show = false
            this.propertyCompanyContactAddForm.$setPristine()
            this.propertyCompanyContactAddForm.$setUntouched()
            this.formSubmitted = false
        }
        // Property Save
        this.propertySave = function(isValid) {
            if (isValid) {
                $scope.formData = {
                    'property_name': this.new.property.property_name,
                    'property_type_id': this.new.property.property_type,
                    'property_street': this.new.property.property_street,
                    'property_city': this.new.property.property_city,
                    'property_state': this.new.property.property_state,
                    'property_zip': this.new.property.property_zip,
                    'property_country': this.new.property.property_country,
                    'mail_to_type_id': this.new.property.mail_to_type_id,
                    'property_id' : this.property_id

                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/property',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.new_property_save = false
                            $scope.propery_add_edit_show_form= true
                            $scope.mvm.property_id= data.property.id

                            if (!$scope.mvm.new.phone.property_phone_id) {
                                if(data.phone.length !=0){
                                    $scope.mvm.new.phone = {
                                        property_phone_id :data.phone[0].id
                                    }
                                }

                            }
                            if (!$scope.mvm.new.contact.property_contact_id) {
                                if(data.contact.length !=0){
                                    $scope.mvm.new.contact = {
                                        property_contact_id :data.contact[0].id
                                    }
                                }
                            }
                            if (!$scope.mvm.new.comment.property_comment_id) {
                                if(data.comment.length !=0) {
                                    $scope.mvm.new.comment = {
                                        property_comment_id: data.comment[0].id
                                    }
                                }
                            }
                            if (!$scope.mvm.new.management.property_company_id) {
                                if(data.company.length !=0) {
                                    $scope.mvm.new.management = {
                                        property_company_id: data.company[0].id
                                    }
                                }
                            }
                            $scope.showAddEditPropertyPhoneList(data)
                            $scope.showAddEditPropertyContactList(data)
                            $scope.showAddEditPropertyCommentList(data)
                            if(data.company.length != 0){
                                $scope.managementcompanyempty = false
                            }else{
                                $scope.managementcompanyempty = true
                            }
                            $scope.showAddEditPropertyCompanyList(data)

                        }
                    })
            }else {
                this.formSubmitted = true
            }
        }
        //property company phone save
        this.propertyCompanyPhoneSave = function(isValid){
            if (isValid) {
                $scope.formData = {
                    'phone_type_id': this.new.companyphone.phone_type_id,
                    'area_code': this.new.companyphone.area_code,
                    'phone_number': this.new.companyphone.phone_number,
                    'phone_ext': this.new.companyphone.phone_ext,
                    'property_id': this.property_id,
                    'management_company_id':this.new.assign_company,
                    'property_phone_id' : this.property_company_phone_add_edit
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertycompanyphone',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickCompanyPhoneNumberCancel()
                            if (!$scope.mvm.new.companyphone.property_phone_id) {
                                if(data.phone.length !=0){
                                    $scope.mvm.new.companyphone = {
                                        property_phone_id :data.phone[0].id
                                    }
                                }
                            }
                            $scope.showAddEditPropertyCompanyPhoneList(data)
                        }
                    })
            }else{
                this.formSubmitted = true
            }
        }

        $scope.onClickEditCompanyPhone = function(){
            if($scope.mvm.new.companyphone.property_phone_id != "") {
                $scope.formData = {
                    'property_phone_id' : $scope.mvm.new.companyphone.property_phone_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyeditcompanyphonedata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.property_company_phone_add_edit = data.property_phone.id
                            $scope.mvm.new.companyphone.phone_type_id = data.phone.phone_type_id
                            $scope.mvm.new.companyphone.area_code = data.phone.area_code
                            $scope.mvm.new.companyphone.phone_number = data.phone.phone_number
                            $scope.mvm.new.companyphone.phone_ext =data.phone.phone_ext
                            $scope.onClickAddCompanyPhone()
                        }
                    })
            }else{
                alert("Please select one company management phone")
            }
        }
        /**** property company contact save *****/
        this.propertyCompanyContactSave = function(isValid){
            if (isValid) {
                $scope.formData = {
                    'contact_type_id': this.new.companycontact.contact_type_id,
                    'first_name': this.new.companycontact.first_name,
                    'last_name': this.new.companycontact.last_name,
                    'email': this.new.companycontact.email,
                    'property_id': this.property_id,
                    'management_company_id':this.new.assign_company,
                    'property_contact_id' : this.property_company_contact_add_edit
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertycompanycontact',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickPropertyCompanyContactCancel()
                            if (!$scope.mvm.new.companycontact.property_contact_id) {
                                if(data.contact.length !=0){
                                    $scope.mvm.new.companycontact = {
                                        property_contact_id :data.contact[0].id
                                    }
                                }
                            }
                            $scope.showAddEditPropertyCompanyContactList(data)
                        }
                    })
            }else{
                this.formSubmitted = true
            }
        }
        $scope.onClickPropertyEditCompanyContact = function(){
            if($scope.mvm.new.companycontact.property_contact_id != "") {
                $scope.formData = {
                    'property_contact_id' : $scope.mvm.new.companycontact.property_contact_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyeditcompanycontactdata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.property_company_contact_add_edit  =data.property_contact.id
                            $scope.mvm.new.companycontact.contact_type_id = data.property_contact.contact_type_id
                            $scope.mvm.new.companycontact.first_name = data.property_contact.first_name
                            $scope.mvm.new.companycontact.last_name = data.property_contact.last_name
                            $scope.mvm.new.companycontact.email =data.property_contact.email
                            $scope.onClickPropertyAddCompanyContact()
                        }
                    })
            }else{
                alert("Please select one management company contact")
            }
        }

        // Property Phone Save
        this.propertyPhoneSave = function(isValid) {
            if (isValid) {
                $scope.formData = {
                    'phone_type_id': this.new.phone.phone_type_id,
                    'area_code': this.new.phone.area_code,
                    'phone_number': this.new.phone.phone_number,
                    'phone_ext': this.new.phone.phone_ext,
                    'property_id': this.property_id,
                    'property_phone_id' : this.property_phone_add_edit
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyphone',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickPhoneNumberCancel()
                            $scope.mvm.property_phone_add_edit =""
                            if (!$scope.mvm.new.phone.property_phone_id) {
                                $scope.mvm.new.phone = {
                                    property_phone_id :data.phone[0].id
                                }
                            }
                            $scope.showAddEditPropertyPhoneList(data)
                        }
                    })
            }else {
                this.formSubmitted = true
            }
        }
        $scope.onClickEditPhone = function(){
            if($scope.mvm.new.phone.property_phone_id != "") {
                $scope.formData = {
                    'property_phone_id' : $scope.mvm.new.phone.property_phone_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyeditphonedata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.property_phone_add_edit = data.property_phone.id
                            $scope.mvm.new.phone.phone_type_id = data.phone.phone_type_id
                            $scope.mvm.new.phone.area_code = data.phone.area_code
                            $scope.mvm.new.phone.phone_number = data.phone.phone_number
                            $scope.mvm.new.phone.phone_ext =data.phone.phone_ext
                            $scope.onClickAddPhone()
                        }
                    })
            }else{
                alert("Please select one property phone")
            }
        }


        /**** property contact save *****/
        this.propertyContactSave = function(isValid){
            if (isValid) {
                $scope.formData = {
                    'contact_type_id': this.new.contact.contact_type_id,
                    'first_name': this.new.contact.first_name,
                    'last_name': this.new.contact.last_name,
                    'email': this.new.contact.email,
                    'property_id': this.property_id,
                    'property_contact_id' : this.property_contact_add_edit
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertycontact',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickPropertyContactCancel()
                            if (!$scope.mvm.new.contact.property_contact_id) {
                                $scope.mvm.new.contact = {
                                    property_contact_id :data.contact[0].id
                                }
                            }
                            $scope.mvm.property_contact_add_edit =""
                            $scope.showAddEditPropertyContactList(data)
                        }
                    })
            }else{
                this.formSubmitted = true
            }
        }
        $scope.onClickPropertyEditContact = function(){
            if($scope.mvm.new.contact.property_contact_id != "") {
                $scope.formData = {
                    'property_contact_id' : $scope.mvm.new.contact.property_contact_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyeditcontactdata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.property_contact_add_edit = data.property_contact.id
                            $scope.mvm.new.contact.contact_type_id = data.property_contact.contact_type_id
                            $scope.mvm.new.contact.first_name = data.property_contact.first_name
                            $scope.mvm.new.contact.last_name = data.property_contact.last_name
                            $scope.mvm.new.contact.email =data.property_contact.email
                            $scope.onClickPropertyAddContact()
                        }
                    })
            }else{
                alert("Please select one property contact")
            }
        }

        /****** Comment Save and Edit ****/
        this.propertyCommentSave = function(isValid){
            if (isValid) {
                $scope.formData = {
                    'comment': this.new.comment.comment,
                    'property_id': this.property_id,
                    'property_comment_id' : this.property_comment_add_edit
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertycomment',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickPropertyCommentCancel()
                            if (!$scope.mvm.new.comment.property_comment_id) {
                                $scope.mvm.new.comment = {
                                    property_comment_id :data.comment[0].id
                                }
                            }
                            $scope.showAddEditPropertyCommentList(data)
                        }
                    })
            }else{
                this.formSubmitted = true
            }
        }

        $scope.onClickPropertyEditComment = function(){
            if($scope.mvm.new.comment.property_comment_id != "") {
                $scope.formData = {
                    'property_comment_id' : $scope.mvm.new.comment.property_comment_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyeditcommentdata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.property_comment_add_edit = data.property_comment.id
                            $scope.mvm.new.comment.comment = data.property_comment.comment
                            $scope.onClickPropertyAddComment()
                        }
                    })
            }else{
                alert("Please select one property comment")
            }
        }

        /****Company Add/ Edit ******/
        this.propertyCompanySave =function(isValid){
            if (isValid) {
                $scope.formData = {
                    'management_company': this.new.management.management_company,
                    'management_company_street': this.new.management.management_company_street,
                    'management_company_city': this.new.management.management_company_city,
                    'management_company_state': this.new.management.management_company_state,
                    'management_company_zip': this.new.management.management_company_zip,
                    'property_id': this.property_id,
                    'property_company_id' : this.property_company_add_edit,
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertycompany',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.onClickPropertyCompanyCancel()
                            if (!$scope.mvm.new.management.property_company_id) {
                                $scope.mvm.new.management = {
                                    property_company_id :data.company[0].id
                                }
                            }
                            if(data.company.length != 0){
                                $scope.managementcompanyempty = false
                            }else{
                                $scope.managementcompanyempty = true
                            }
                            $scope.showAddEditPropertyCompanyList(data)
                        }
                    })
            }else{
                this.formSubmitted = true
            }
        }

        $scope.onClickPropertyEditManagementCompany = function(){
            if($scope.mvm.new.management.property_company_id != "") {
                $scope.formData = {
                    'property_company_id' : $scope.mvm.new.management.property_company_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyeditcompanydata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.property_company_add_edit = data.property_company.id
                            $scope.mvm.new.management.management_company = data.property_company.management_company
                            $scope.mvm.new.management.management_company_street = data.property_company.management_company_street
                            $scope.mvm.new.management.management_company_city = data.property_company.management_company_city
                            $scope.mvm.new.management.management_company_state = data.property_company.management_company_state
                            $scope.mvm.new.management.management_company_zip = data.property_company.management_company_zip
                            $scope.onClickPropertyAddManagementCompany()
                        }
                    })
            }else{
                alert("Please select one property company")
            }
        }
        $scope.onClickPropertyAssignManagementCompany = function(){
            if($scope.mvm.new.management.property_company_id != "") {
                $scope.formData = {
                    'property_company_id' : $scope.mvm.new.management.property_company_id
                }
                $http({
                    method  : 'POST',
                    url     : '/api/property/propertyassigncompanydata',
                    data    : $.param($scope.formData),  // pass in data as strings
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                    .success(function(data) {
                        if(data.response.result == "success"){
                            $scope.mvm.new.assign_company = data.property_company.id
                            $scope.assign_company = true
                            $scope.mvm.new.assignmanagement.management_company = data.property_company.management_company
                            $scope.mvm.new.assignmanagement.management_company_street = data.property_company.management_company_street
                            $scope.mvm.new.assignmanagement.management_company_city = data.property_company.management_company_city
                            $scope.mvm.new.assignmanagement.management_company_state = data.property_company.management_company_state
                            $scope.mvm.new.assignmanagement.management_company_zip = data.property_company.management_company_zip
                            if (!$scope.mvm.new.companyphone.property_phone_id) {
                                if(data.phone.length !=0){
                                    $scope.mvm.new.companyphone = {
                                        property_phone_id :data.phone[0].id
                                    }
                                }
                            }
                            if (!$scope.mvm.new.companycontact.property_contact_id) {
                                if(data.contact.length !=0){
                                    $scope.mvm.new.companycontact = {
                                        property_contact_id :data.contact[0].id
                                    }
                                }
                            }
                            $scope.showAddEditPropertyCompanyPhoneList(data)
                            $scope.showAddEditPropertyCompanyContactList(data)

                        }
                    })
            }else{
                alert("Please select one property company")
            }
        }

        /**** property company phone show  functions ****/
        $scope.showAddEditPropertyCompanyPhone = function(){
            let dataSet = $scope.companyphones
            $scope.mvm.displayTable4 = true
            $scope.mvm.dtOptions4 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns4 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyCompanyPhoneHtml),
                DTColumnBuilder.newColumn('phone_type').withTitle('Phone Type'),
                DTColumnBuilder.newColumn('area_code').withTitle('Area Code'),
                DTColumnBuilder.newColumn('phone_number').withTitle('Phone Number'),
                DTColumnBuilder.newColumn('phone_ext').withTitle('Phone Ext'),
            ]
        }
        $scope.showAddEditPropertyCompanyPhoneList = function(data){
            let list = []
            angular.forEach(data.phone, function(value){
                list.push({id: value.id, phone_type: value.phone_type_id, area_code: value.area_code, phone_number : value.phone_number, phone_ext:value.phone_ext})
            })
            $scope.companyphones = list
            $scope.showAddEditPropertyCompanyPhone()
        }

        let actionsPropertyCompanyPhoneHtml = (data) =>{
            return `
                <input type="radio"  name="property_phone_id" value="${data.id}" ng-model="mvm.new.companyphone.property_phone_id">
                `
        }


        /**** property phone show  functions ****/
        $scope.showAddEditPropertyPhone = function(){
            let dataSet = $scope.phones
            $scope.mvm.displayTable = true
            $scope.mvm.dtOptions = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyPhoneHtml),
                DTColumnBuilder.newColumn('phone_type').withTitle('Phone Type'),
                DTColumnBuilder.newColumn('area_code').withTitle('Area Code'),
                DTColumnBuilder.newColumn('phone_number').withTitle('Phone Number'),
                DTColumnBuilder.newColumn('phone_ext').withTitle('Phone Ext'),
            ]
        }
        $scope.showAddEditPropertyPhoneList = function(data){
            let list = []
            angular.forEach(data.phone, function(value){
                list.push({id: value.id, phone_type: value.phone_type_id, area_code: value.area_code, phone_number : value.phone_number, phone_ext:value.phone_ext})
            })
            $scope.phones = list
            $scope.showAddEditPropertyPhone()
        }

        let actionsPropertyPhoneHtml = (data) =>{
            return `
                <input type="radio"  name="property_phone_id" value="${data.id}" ng-model="mvm.new.phone.property_phone_id" ng-select = "{data.id} == {$scope.mvm.new.phone.property_phone_id}">
                `
        }
        /*** property Contact Show Functions ****/
        $scope.ShowAddEditPropertyContact = function(){
            let dataSet = $scope.contacts
            $scope.mvm.displayTable1 = true
            $scope.mvm.dtOptions1 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns1 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyContactHtml),
                DTColumnBuilder.newColumn('contact_type').withTitle('Contact Type'),
                DTColumnBuilder.newColumn('first_name').withTitle('First Name'),
                DTColumnBuilder.newColumn('last_name').withTitle('Last Name'),
                DTColumnBuilder.newColumn('email').withTitle('Email'),
            ]
        }

        $scope.showAddEditPropertyContactList = function(data){
            let list = []
            angular.forEach(data.contact, function(value){
                list.push({id: value.id, contact_type: value.contact_type_id, first_name: value.first_name, last_name : value.last_name, email:value.email})
            })
            $scope.contacts = list
            $scope.ShowAddEditPropertyContact()
        }
        let actionsPropertyContactHtml = (data) =>{
            return `
                <input type="radio"  name="property_contact_id" value="${data.id}" ng-model="mvm.new.contact.property_contact_id" ng-select = "{data.id} == {$scope.mvm.new.contact.property_contact_id}">
                `
        }




        /*** property Comment Show Functions ****/
        $scope.ShowAddEditPropertyComment = function(){
            let dataSet = $scope.comments
            $scope.mvm.displayTable2 = true
            $scope.mvm.dtOptions2 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns2 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyCommentHtml),
                DTColumnBuilder.newColumn('comment').withTitle('Comment'),
                DTColumnBuilder.newColumn('creator_id').withTitle('Creator'),
            ]
        }
        $scope.showAddEditPropertyCommentList = function(data){
            let list = []
            angular.forEach(data.comment, function(value){
                list.push({id: value.id, comment: value.comment,creator_id: value.creator_id})
            })
            $scope.comments = list
            $scope.ShowAddEditPropertyComment()
        }
        let actionsPropertyCommentHtml = (data) =>{
            return `
                <input type="radio"  name="property_comment_id" value="${data.id}" ng-model="mvm.new.comment.property_comment_id" ng-select = "{data.id} == {$scope.mvm.new.comment.property_comment_id}">
                `
        }

        /*** Property Management Company Show Function****/

        $scope.ShowAddEditPropertyCompany = function(){
            let dataSet = $scope.companies
            $scope.mvm.displayTable3 = true
            $scope.mvm.dtOptions3 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns3 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyCompanyHtml),
                DTColumnBuilder.newColumn('management_company').withTitle('Company Name'),
                DTColumnBuilder.newColumn('management_company_street').withTitle('Address'),
                DTColumnBuilder.newColumn('management_company_city').withTitle('City'),
                DTColumnBuilder.newColumn('management_company_state').withTitle('State'),
                DTColumnBuilder.newColumn('management_company_zip').withTitle('Zip'),
            ]
        }
        $scope.showAddEditPropertyCompanyList = function(data){
            let list = []
            angular.forEach(data.company, function(value){
                list.push({id: value.id, management_company: value.management_company,management_company_street: value.management_company_street, management_company_city: value.management_company_city,management_company_state: value.management_company_state,management_company_zip: value.management_company_zip})
            })
            $scope.companies = list
            $scope.ShowAddEditPropertyCompany()
        }

        let actionsPropertyCompanyHtml = (data) =>{
            return `
                <input type="radio"  name="property_company_id" value="${data.id}" ng-model="mvm.new.management.property_company_id" ng-select = "{data.id} == {$scope.mvm.new.management.property_company_id}">
                `
        }
        /*** property CompanyContact Show Functions ****/
        $scope.ShowAddEditPropertyCompanyContact = function(){
            let dataSet = $scope.companycontacts
            $scope.mvm.displayTable5 = true
            $scope.mvm.dtOptions5 = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.mvm.dtColumns5 = [
                DTColumnBuilder.newColumn(null).withTitle('#').renderWith(actionsPropertyCompanyContactHtml),
                DTColumnBuilder.newColumn('contact_type').withTitle('Contact Type'),
                DTColumnBuilder.newColumn('first_name').withTitle('First Name'),
                DTColumnBuilder.newColumn('last_name').withTitle('Last Name'),
                DTColumnBuilder.newColumn('email').withTitle('Email'),
            ]
        }

        $scope.showAddEditPropertyCompanyContactList = function(data){
            let list = []
            angular.forEach(data.contact, function(value){
                list.push({id: value.id, contact_type: value.contact_type_id, first_name: value.first_name, last_name : value.last_name, email:value.email})
            })
            $scope.companycontacts = list
            $scope.ShowAddEditPropertyCompanyContact()
        }
        let actionsPropertyCompanyContactHtml = (data) =>{
            return `
                <input type="radio"  name="property_contact_id" value="${data.id}" ng-model="mvm.new.companycontact.property_contact_id" ng-select = "{data.id} == {$scope.mvm.new.companycontact.property_contact_id}">
                `
        }
        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }

    }
    save(isValid) {
        if (isValid){
            let $state = this.$state

            this.$scope.formData ={
                'requester_name' : this.$scope.vm.requester_name,
                'requester_phone' : this.$scope.vm.requester_phone,
                'requester_fax' : this.$scope.vm.requester_fax,
                'scope' :  this.$scope.vm.scope,
                'details' : this.$scope.vm.details,
                'source_id' : this.$scope.vm.source_id,
                'bid_statuses_id' : this.$scope.vm.bid_statuses_id,
                'estimator_id' : this.$scope.vm.estimator_id,
                'property_id' : this.$scope.assign_property_id,
                'request_id': this.$scope.assign_request_id
            }
            this.$http({
                method  : 'POST',
                url     : '/api/request/request',
                data    : $.param(this.$scope.formData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
                .success(function(data) {
                    if(data.response.result == "success"){
                        $state.go('app.requests')
                    }
                })
        }
        else {
            this.formSubmitted = true
        }
    }

    $onInit(){
    }
}

export const RequestEditComponent = {
    templateUrl: './views/app/components/request-edit/request-edit.component.html',
    controller: RequestEditController,
    controllerAs: 'vm',
    bindings: {}
}
