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

use App\Property_management_company;

use App\Property_management_company_contact;

use App\Property_management_company_phone;

use App\Property_phone;

use App\Phone_type;

use App\Request_comment;

use App\Request_file;

class RequestController extends Controller
{
    public function postRequest(Request $request){
        $request_id = $request->input('request_id');
        if($request_id !=""){
            $requestModel = RequestModel::where('id',$request_id)->first();
            $requestModel->update($request->all());
            $requestModel->editor_id = Auth::user()->id;
            $requestModel->save();
        }else{
            $requestModel = RequestModel::create($request->all());
            $requestModel->assign_date = Carbon::today()->format('Y-m-d');
            $requestModel->creator_id = Auth::user()->id;
            $requestModel->assign_id = Auth::user()->id;
            $requestModel->editor_id = Auth::user()->id;
            $requestModel->save();
        }
        $data['result'] = 'success';
        return response()->json(['response' => $data]);
    }

    public function getIndex(){
        $requestLists = RequestModel::all();
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
        if($requestModel->property_id != 0){
            $resultArray['property'] = Property::where('id', $requestModel->property_id)->first();
        }else{
            $resultArray['property'] = "";
        }
        if($requestModel->property_phone_id !=0){
            $property_phone = Property_phone::where('id',$requestModel->property_phone_id)->first();
            if($property_phone->phone_type_id  == $phone_fax_type->id){
                $resultArray['phone'] = "";
                $resultArray['phone_fax'] = $property_phone->area_code. " " . $property_phone->phone_number ." " . $property_phone->phone_ext;
            }else{
                $resultArray['phone'] = $property_phone->area_code. " " . $property_phone->phone_number ." " . $property_phone->phone_ext;
                $resultArray['phone_fax'] = "";
            }
        }else{
            $resultArray['phone'] = "";
            $resultArray['phone_fax'] ="";
        }

        if($requestModel->property_contact_id != "") {
            $property_contact = Property_contact::where('id',$requestModel->property_contact_id)->first();
            $resultArray['contact_name'] = $property_contact->first_name." " . $property_contact->last_name;
        }else{
            $resultArray['contact_name'] =  "";
        }


        if($requestModel->property_company_id != "") {
            $property_company = Property_management_company::where('id',$requestModel->property_company_id)->first();
            $resultArray['property_company_list'] = $property_company;
        }else{
            $resultArray['property_company_list'] = "";
        }
        if($requestModel->property_company_phone_id != "") {
            $property_company_phone = Property_management_company_phone::where('id',$requestModel->property_company_phone_id)->first();
            if($property_company_phone->phone_type_id  == $phone_fax_type->id){
                $resultArray['company_phone'] = "";
                $resultArray['company_phone_fax'] =$property_company_phone->area_code. " " . $property_company_phone->phone_number ." " . $property_company_phone->phone_ext;
            }else{
                $resultArray['company_phone'] = $property_company_phone->area_code. " " . $property_company_phone->phone_number ." " . $property_company_phone->phone_ext;
                $resultArray['company_phone_fax'] ="";
            }
        }else{
            $resultArray['company_phone'] = "";
            $resultArray['company_phone_fax'] ="";
        }
        if($requestModel->property_company_contact_id !="") {
            $property_company_contact = Property_management_company_contact::where('id',$requestModel->property_company_phone_id)->first();
            $resultArray['company_contact_name'] = $property_company_contact->first_name." " . $property_company_contact->last_name;
        }else{
            $resultArray['company_contact_name'] = "";
        }

        $comments = Request_comment::where('request_id', $requestId)->get();
        $commentArray = $this->getRequestCommentList($comments);
        return response()->json(['response' =>$resultArray, 'request' =>$requestModel, 'comment' =>$commentArray]);
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
