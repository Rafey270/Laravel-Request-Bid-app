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

use App\Bid_type;

use App\Request_line_item;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BidController extends Controller
{
    public function postBid(Request $request){
        $bidType = $request->input('bid_type_id');
        $bidTypeId = Bid_type::where('name', $bidType)->first();
        $requestId= $request->input('request_id');
        if($requestId != ""){
            $formData = array(
                'due_date' => $request->input('due_date'),
                'archive_bid' =>$request->input('archive_bid'),
                'scope'     => $request->input('scope'),
                'details'   => $request->input('details'),
                'source_id' =>$request->input('source_id'),
                'bid_statuses_id' =>$request->input('bid_statuses_id'),
                'estimator_id' =>$request->input('estimator_id'),
                'property_id'  => $request->input('property_id')
            );
            $formData['bid_type_id'] = $bidTypeId->id;
            if($bidType == "Standard"){
                $formData['bid_template_id'] = $request->input('bid_template_id');
            }
            $requestModel = RequestModel::where('id', $requestId)->first();
            $requestModel->update($formData);
        }else{
            $formData = array(
                'request_name' =>'',
                'request_phone' =>'',
                'request_fax' =>'',
                'creator_id' => Auth::user()->id,
                'editor_id' => Auth::user()->id,
                'assign_id' => Auth::user()->id,
                'due_date' => $request->input('due_date'),
                'archive_bid' =>$request->input('archive_bid'),
                'scope'     => $request->input('scope'),
                'details'   => $request->input('details'),
                'source_id' =>$request->input('source_id'),
                'bid_statuses_id' =>$request->input('bid_statuses_id'),
                'estimator_id' =>$request->input('estimator_id'),
                'property_id'  => $request->input('property_id')
            );
            $formData['bid_type_id'] = $bidTypeId->id;
            if($bidType == "Standard"){
                $formData['bid_template_id'] = $request->input('bid_template_id');
            }
            $requestModel = RequestModel::create($formData);
        }
        $requestBidJob = Request_bid_job::where('name','Bid')->first();
        $requestModel->request_bid_job_id = $requestBidJob->id;
        $requestModel->save();

        return response()->json(['response' =>$requestModel]);

    }

    public function getIndex(){
        $requestBidJob = Request_bid_job::where('name','Bid')->first();
        $requestLists = RequestModel::where('request_bid_job_id', $requestBidJob->id)->get();

        $requestListArray = array();
        if(count($requestLists)>0){
            foreach ($requestLists as $key_request => $requestList){
                //print_r($requestList->bidType);
                $requestListArray[$key_request]['id'] = $requestList->id;
                if($requestList->bid_type_id != "0"){
                    $requestListArray[$key_request]['bid_type_id'] = $requestList->bidType->name;
                }else{
                    $requestListArray[$key_request]['bid_type_id'] =  "";
                }
                if($requestList->bid_template_id != "0") {
                    if ($requestList->bidType->name == "Standard") {
                        $requestListArray[$key_request]['bid_template_id'] = $requestList->bidTemplate->name;
                    } else {
                        $requestListArray[$key_request]['bid_template_id'] = "";
                    }
                }else{
                    $requestListArray[$key_request]['bid_template_id'] = "";
                }
                $requestListArray[$key_request]['requester_phone'] = $requestList->requester_phone;
                $requestListArray[$key_request]['requester_name'] = $requestList->requester_name;
                $requestListArray[$key_request]['assign_by'] = $requestList->assignBy->name;
                $requestListArray[$key_request]['assign_to'] = $requestList->estimator->name;
            }
        }

        return response()->json(['response' =>$requestListArray]);
    }


    public function postGetbid(Request $request){
        $bidId = $request->input('bidId');
        $requestModel = RequestModel::where('id', $bidId)->first();
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


        $comments = Request_comment::where('request_id', $bidId)->get();
        $commentArray = $this->getRequestCommentList($comments);
        $line_items = Request_line_item::where('request_id', $bidId)->get();
        $lineItemsArray = $this->getRequestLineItemList($line_items);

        $request_files = Request_file::where('request_id', $bidId)->get();
        $requestFileList = $this->getRequestFileItemList($request_files);

        if($requestModel->bid_type_id != "0"){
            $bidType = Bid_type::where('id',$requestModel->bid_type_id)->first();
        }else{
            $bidType = "";
        }
        return response()->json(['response' =>$resultArray, 'request' =>$requestModel, 'comment' =>$commentArray,'property_list' =>$resultProperties,'bidType' =>$bidType,'line_items' =>$lineItemsArray, 'request_files' =>$requestFileList]);
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


    public function postBidline(Request $request){
        $request_line_id = $request->input('request_line_id');
        if($request_line_id  != ""){
            $requestBidLine = Request_line_item::where('id',$request_line_id)->first();
            $requestBidLine->line_item_description = $request->input('line_item_description');
            $requestBidLine->lump_sum = $request->input('lump_sum');
            $requestBidLine->save();
        }else{
            $requestBidLine = new Request_line_item();
            $requestBidLine->line_item_description = $request->input('line_item_description');
            $requestBidLine->lump_sum = $request->input('lump_sum');
            $requestBidLine->creator_id = Auth::user()->id;
            $requestBidLine->request_id = $request->input('request_id');
            $requestBidLine->save();
        }
        $bidId = $request->input('request_id');
        $line_items = Request_line_item::where('request_id', $bidId)->get();
        $lineItemsArray = $this->getRequestLineItemList($line_items);
        $data['result'] = 'success';
        $data['type'] = 'created';
        return response()->json(['response' => $data, 'line_items' =>$lineItemsArray]);
    }

    function getRequestLineItemList($line_items){
        $lineItemsArrayList = array();
        if(count($line_items)>0){
            foreach ($line_items as $key_line_item =>$line_item){
                $creator = $line_item->creator;
                $lineItemsArrayList[$key_line_item]['id'] = $line_item->id;
                $lineItemsArrayList[$key_line_item]['line_item_description'] = $line_item->line_item_description;
                $lineItemsArrayList[$key_line_item]['creator_id'] = $creator->name;
                $lineItemsArrayList[$key_line_item]['lump_sum'] = $line_item->lump_sum;
                $lineItemsArrayList[$key_line_item]['order'] = $line_item->order;
                $lineItemsArrayList[$key_line_item]['option_flag'] = $line_item->option_flag;
                $lineItemsArrayList[$key_line_item]['created_at'] = substr($line_item->created_at,0,10);
            }
        }
        return $lineItemsArrayList;
    }
    function getRequestFileItemList($request_files){
        $requestFileListArray = array();
        foreach ($request_files as $key_request_file => $request_file){
            $requestFileListArray[$key_request_file]['id'] = $request_file->id;
            $requestFileListArray[$key_request_file]['file_type'] = $request_file->fileType->name;
            $requestFileListArray[$key_request_file]['description'] = $request_file->description;
            $requestFileListArray[$key_request_file]['file_name'] = $request_file->file_name;
            $requestFileListArray[$key_request_file]['creator_id'] = $request_file->creator->name;
            $requestFileListArray[$key_request_file]['created_at'] = substr($request_file->created_at,0,10);
        }
        return $requestFileListArray;
    }

    public function postBideditlineitemdata(Request $request){
        $property_item_id  = $request->input('property_item_id');
        $line_item = Request_line_item::where('id', $property_item_id)->first();
        $data['result']= "success";
        return response()->json(['response' => $data,'request_line_item' =>$line_item]);
    }


    public function postBidfile(Request $request){
        $file = $request->file('file');
        $request_id = $request->input('request_id');
        $request_file_type_id = $request->input('file_type_id');
        $request_file_id = $request->input('request_file_id');
        if($request->hasFile('file')){
            $extension = $file->getClientOriginalExtension();
            $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());

            $change_name = $timestamp.".".$extension;
//            Storage::disk('local')->put($change_name,  File::get($file));
            $file->move(
                base_path() . '/public/upload-file/', $change_name
            );

            if($request_file_id != ""){
                $requestFile = Request_file::where('id', $request_file_id)->first();
            }else{
                $requestFile = new Request_file();
            }
            $requestFile->file_type = $request_file_type_id;
            $requestFile->description =$request->input('description');
            $requestFile->file_name = $file->getClientOriginalName();
            $requestFile->mine = $file->getClientMimeType();
            $requestFile->file_change_name = $change_name;
            $requestFile->request_id =$request_id;
            $requestFile->creator_id = Auth::user()->id;
            $requestFile->save();
        }else{
            if($request_file_id != ""){
                $requestFile = Request_file::where('id', $request_file_id)->first();
                $requestFile->file_type = $request_file_type_id;
                $requestFile->description =$request->input('description');
                $requestFile->save();
            }
        }

        $request_files = Request_file::where('request_id', $request_id)->get();
        $requestFileList = $this->getRequestFileItemList($request_files);
        return response()->json(['request_files' =>$requestFileList]);
    }


    public function postBideditfiledata(Request $request){
        $request_file_id = $request->input('property_file_id');

        $requestFile = Request_file::where('id', $request_file_id)->first();
        $data['result'] ='success';
        return response()->json(['request_file' => $requestFile,  'response' =>$data]);
    }

    public function postStoragefile(Request $request){
//        print_r($request->all());
//        exit;
        $filename = $request->input('file_name');
        $entry = Request_file::where('file_change_name', $filename)->first();
        $file = Storage::disk('local')->get($entry->file_change_name);
        $path = storage_path()."/app/".$entry->file_change_name;
        return (new Response($file, 200))->header('Content-Type', $entry->mime);
//        return response()->download($path,$filename);
//        $headers = array('Content-Type', $entry->mime);
//        return Response::download($path, $filename, $headers);
    }
    public function getStoragefile($filename){
        $entry = Request_file::where('file_change_name', $filename)->first();
        $file = Storage::disk('local')->get($entry->file_change_name);
//        $headers = array('Content-Type: image/png');
//        $headers = array(
//            'Content-Type' => $entry->mine,
//            'Content-Desposition' => "attachment; filename='".$filename."'"
//        );
        $path = storage_path()."/app/".$entry->file_change_name;
//        return \Response::download($path,$filename,$headers);

        $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        echo $storagePath."/".$entry->file_change_name;
        exit;
        return (new Response($file, 200))->header('Content-Type', $entry->mine);


    }




}
