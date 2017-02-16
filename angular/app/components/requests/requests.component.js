class RequestsController{
    constructor($scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API, $http) {
        'ngInject';

        this.API = API
        this.$state = $state
        this.$scope = $scope
        $http.get('/api/request/').success(function(data,status,headers, config){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, requester_name: value.requester_name, requester_phone: value.requester_phone, assign_by : value.assign_by, assign_to:value.assign_to})
            })
            let dataSet = list;
            $scope.vm.dtOptions = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.vm.dtColumns = [
                DTColumnBuilder.newColumn('id').withTitle('ID'),
                DTColumnBuilder.newColumn('requester_name').withTitle('Requester Name'),
                DTColumnBuilder.newColumn('requester_phone').withTitle('Requester Phone'),
                DTColumnBuilder.newColumn('assign_by').withTitle('Assign By'),
                DTColumnBuilder.newColumn('assign_to').withTitle('Assign To'),
                DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                    .renderWith(actionsHtml)
            ]
            $scope.vm.displayTable = true
        })
        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }
        let actionsHtml = (data) => {
            return `
                <a class="btn btn-xs btn-warning" ui-sref="app.requestsedit({requestId: ${data.id}})">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button class="btn btn-xs btn-danger" ng-click="vm.delete(${data.id})">
                    <i class="fa fa-trash-o"></i>
                </button>`
        }

    }
    delete(requestId) {
        let API = this.API
        let $state = this.$state

        swal({
            title: 'Are you sure?',
            text: 'You will not be able to recover this data!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, delete it!',
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            html: false
        }, function () {
        API.one('request').one('request', requestId).remove()
            .then(() => {
                swal({
                    title: 'Deleted!',
                    text: 'Request has been deleted.',
                    type: 'success',
                    confirmButtonText: 'OK',
                    closeOnConfirm: true
                }, function () {
                    $state.reload()
                })
            })
        })
    }
    $onInit(){
    }
}

export const RequestsComponent = {
    templateUrl: './views/app/components/requests/requests.component.html',
    controller: RequestsController,
    controllerAs: 'vm',
    bindings: {}
}
