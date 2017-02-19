<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Request as RequestModel;

use Auth;

use Carbon\Carbon;

use App\Property;

use App\Property_type;

use App\Property_contact;

use App\Property_comment;

use App\Management_company;

use App\Management_company_contact;

use App\Management_company_phone;

use App\Property_phone;

use App\Phone_type;

use App\Request_comment;

use App\Request_file;

use App\Phone;

use App\Request_bid_job;

class RequestController extends Controller
{
    public function postRequestbid(Request $request){
        $request_id = $request->input('request_id');
        $requestModel = RequestModel::where('id', $request_id)->first();
        $requestBidJob = Request_bid_job::where('name','Bid')->first();
        $requestModel->request_bid_job_id = $requestBidJob->id;
        $requestModel->save();
        return response()->json(['response' =>$requestModel]);
    }
    public function postRequest(Request $request){
        $request_id = $request->input('request_id');
        if($request_id !=""){
            $requestModel = RequestModel::where('id',$request_id)->first();
            $requestModel->update($request->all());
            $requestModel->editor_id = Auth::user()->id;
            $requestModel->save();
        }else{
            $requestModel = RequestModel::create($request->all());
            $requestBidJob = Request_bid_job::where('name','Request')->first();
            $requestModel->assign_date = Carbon::today()->format('Y-m-d');
            $requestModel->creator_id = Auth::user()->id;
            $requestModel->assign_id = Auth::user()->id;
            $requestModel->editor_id = Auth::user()->id;
            $requestModel->approved_id = Auth::user()->id;
            $requestModel->request_bid_job_id = $requestBidJob->id;

            $requestModel->save();
        }
        $data['result'] = 'success';
        return response()->json(['response' => $data]);
    }

    public function getIndex(){
        $requestBidJob = Request_bid_job::where('name','Request')->first();
        $requestLists = RequestModel::where('request_bid_job_id', $requestBidJob->id)->get();

        $requestListArray = array();
        if(count($requestLists)>0){
            foreach ($requestLists as $key_request => $requestList){
                $requestListArray[$key_request]['id'] = $requestList->id;
                $requestListArray[$key_request]['requester_phone'] = $requestList->requester_phone;
                $requestListArray[$key_request]['requester_name'] = $requestList->requester_name;
                $requestListArray[$key_request]['assign_by'] = $requestList->assignBy->name;
                $requestListArray[$key_request]['assign_to'] = $requestList->estimator->name;
            }
        }

        return response()->json(['response' =>$requestListArray]);
    }
    public function deleteRequest($id)
    {
        RequestModel::destroy($id);

        return response()->success('success');
    }

    public function postGetrequest(Request $request){
        $requestId = $request->input('requestId');
        $requestModel = RequestModel::where('id', $requestId)->first();
        $phone_fax_type = Phone_type::where('name','Fax')->first();
        $resultArray = array();
        $resultProperties = array();
        if($requestModel->property_id != 0){
            $resultArray['property'] = Property::where('id', $requestModel->property_id)->first();
            $property = Property::where('id', $requestModel->property_id)->first();
            $resultProperties['property_id'] = $requestModel->property_id;
            $propertyPhones = Property_phone::where('property_id' , $requestModel->property_id)->get();
            if(count($propertyPhones)>0){
                $property_phone = Property_phone::where('property_id',$requestModel->property_id)->first();
                $resultProperties['property_phone_id'] = $property_phone->id;
                $phone = Phone::where('id', $property_phone->phone_id)->first();
                if($phone->phone_type_id  == $phone_fax_type->id){
                    $resultArray['phone'] = "";
                    $resultArray['phone_fax'] = $phone->area_code. " " . $phone->phone_number ." " . $phone->phone_ext;
                }else{
                    $resultArray['phone'] = $phone->area_code. " " . $phone->phone_number ." " . $phone->phone_ext;
                    $resultArray['phone_fax'] = "";
                }
            }else{
                $resultArray['phone'] = "";
                $resultArray['phone_fax'] ="";
                $resultProperties['proerty_id'] = "";
            }
            $propertyContacts = Property_contact::where('property_id',$requestModel->property_id)->get();
            if(count($propertyContacts)>0) {
                $property_contact = Property_contact::where('property_id',$requestModel->property_id)->first();
                $resultProperties['property_contact_id'] = $property_contact->id;
                $resultArray['contact_name'] = $property_contact->first_name." " . $property_contact->last_name;
            }else{
                $resultArray['contact_name'] =  "";
                $resultProperties['property_contact_id'] = "";
            }
            if($property->management_company_id != 0){
                $property_company = Management_company::where('id',$property->management_company_id)->first();
                $resultArray['property_company_list'] = $property_company;
                $resultProperties['property_company_id'] = $property_company->id;
            }else{
                $resultArray['property_company_list'] = "";
                $resultProperties['property_company_id'] = "";
            }
            if($property->management_company_id != 0) {
                $managementCompanyPhones = Management_company_phone::where('management_company_id',$property->management_company_id)->get();
                if (count($managementCompanyPhones)>0) {
                    $property_company_phone = Management_company_phone::where('management_company_id',$property->management_company_id)->first();
                    $phone = Phone::where('id', $property_company_phone->phone_id)->first();
                    $resultProperties['property_company_phone_id'] = $property_company_phone->id;
                    if ($phone->phone_type_id == $phone_fax_type->id) {
                        $resultArray['company_phone'] = "";
                        $resultArray['company_phone_fax'] = $phone->area_code . " " . $phone->phone_number . " " . $phone->phone_ext;
                    } else {
                        $resultArray['company_phone'] = $phone->area_code . " " . $phone->phone_number . " " . $phone->phone_ext;
                        $resultArray['company_phone_fax'] = "";
                    }
                } else {
                    $resultArray['company_phone'] = "";
                    $resultArray['company_phone_fax'] = "";
                    $resultProperties['property_company_phone_id'] = "";
                }
            }else{
                $resultArray['company_phone'] = "";
                $resultArray['company_phone_fax'] = "";
                $resultProperties['property_company_phone_id'] = "";
            }
            if($property->management_company_id != 0) {
                $managementCompanyContacts = Management_company_contact::where('management_company_id', $property->management_company_id)->get();
                if (count($managementCompanyContacts)>0) {

                    $property_company_contact = Management_company_contact::where('management_company_id', $property->management_company_id)->first();
                    $resultProperties['property_company_contact_id'] = $property_company_contact->id;
                    $resultArray['company_contact_name'] = $property_company_contact->first_name . " " . $property_company_contact->last_name;
                } else {
                    $resultArray['company_contact_name'] = "";
                    $resultProperties['property_company_contact_id'] = "";
                }
            }else{
                $resultArray['company_contact_name'] = "";
                $resultProperties['property_company_contact_id'] = "";
            }
        }else{
            $resultArray['property'] = "";
            $resultArray['phone'] = "";
            $resultArray['phone_fax'] ="";
            $resultArray['contact_name'] =  "";
            $resultArray['company_phone'] = "";
            $resultArray['company_phone_fax'] ="";
            $resultArray['company_contact_name'] = "";
            $resultArray['property_company_list'] = "";
        }


        $comments = Request_comment::where('request_id', $requestId)->get();
        $commentArray = $this->getRequestCommentList($comments);
        return response()->json(['response' =>$resultArray, 'request' =>$requestModel, 'comment' =>$commentArray,'property_list' =>$resultProperties]);
    }



    public function postRequestcomment(Request $request){
        $request_comment_id = $request->input('request_comment_id');

        if($request_comment_id !=""){
            $request_comment = Request_comment::where('id', $request_comment_id)->first();
            $request_comment->comment = $request->input('comment');
            $request_comment->save();
        }else{
            $request_comment =  new Request_comment();
            $request_comment->comment = $request->input('comment');
            $request_comment->creator_id = Auth::user()->id;
            $request_comment->request_id = $request->input('request_id');
            $request_comment->save();
        }
        $data['result'] = 'success';
        $data['type'] = 'created';
        $request_id = $request->input('request_id');
        $comments = Request_comment::where('request_id' ,$request_id)->get();
        $commentArray = $this->getRequestCommentList($comments);
        return response()->json(['response' => $data, 'comment' =>$commentArray]);
    }

    function getRequestCommentList($comments){
        $commentArrayList = array();
        if(count($comments)>0){
            foreach ($comments as $key_comment =>$comment){
                $creator = $comment->creator;
                $commentArrayList[$key_comment]['id'] = $comment->id;
                $commentArrayList[$key_comment]['comment'] = $comment->comment;
                $commentArrayList[$key_comment]['creator_id'] = $creator->name;
            }
        }
        return $commentArrayList;
    }

    public function postRequesteditcommentdata(Request $request){
        $request_comment_id = $request->input('request_comment_id');
        $request_comment = Request_comment::where('id',$request_comment_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'request_comment' => $request_comment]);
    }
}
