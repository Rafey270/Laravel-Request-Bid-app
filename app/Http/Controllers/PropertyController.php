<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Property;

use App\Property_type;

use App\Property_contact;

use App\Property_comment;

use App\Management_company;

use App\Management_company_contact;

use App\Management_company_phone;

use App\Property_phone;

use App\Phone_type;

use App\Phone;


use Auth;

class PropertyController extends Controller
{

    public function getProperties(Request $request){
        $properties = Property::all();
        return response()->json(['response' => $properties]);
    }

    public function postAssignproperty(Request $request){
        $property_id = $request->input('property_id');
        $phone_fax_type = Phone_type::where('name','Fax')->first();
        $resultArray = array();
        if($property_id != "") {
            $property = Property::where('id',$property_id)->first();
        }
        //$property_phone_id = $request->input('phone_id');
        $property_phones = Property_phone::where('property_id', $property_id)->first();
        if(count($property_phones) >0) {
            $property_phone = Phone::where('id',$property_phones->phone_id)->first();
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
        //$property_contact_id = $request->input('contact_id');
        $property_contact = Property_contact::where('property_id', $property_id)->first();
        if(count($property_contact) >0) {

            //$property_contact = Property_contact::where('id',$property_contact_id)->first();
            $resultArray['contact_name'] = $property_contact->first_name." " . $property_contact->last_name;
        }else{
            $resultArray['contact_name'] =  "";
        }
        $property_contact_phone_id = $request->input('contact_phone_id');
        $property_company_id = $request->input('company_id');

        if($property->management_company_id != 0) {
            $property_company = Management_company::where('id',$property->management_company_id)->first();
            $resultArray['property_company_list'] = $property_company;
        }else{
            $resultArray['property_company_list'] = "";
        }
        $property_company_phone_id = $request->input('company_phone_id');
        if($property->management_company_id !=0){
            $property_company_phones = Management_company_phone::where('management_company_id',$property->management_company_id)->get();
            if(count($property_company_phones)>0) {
                $property_company_phone = Management_company_phone::where('management_company_id',$property->management_company_id)->first();
                $phone = Phone::where('id',$property_company_phone->phone_id)->first();
                if($phone->phone_type_id  == $phone_fax_type->id){
                    $resultArray['company_phone'] = "";
                    $resultArray['company_phone_fax'] =$phone->area_code. " " . $phone->phone_number ." " . $phone->phone_ext;
                }else{
                    $resultArray['company_phone'] = $phone->area_code. " " . $phone->phone_number ." " . $phone->phone_ext;
                    $resultArray['company_phone_fax'] ="";
                }
            }else{
                $resultArray['company_phone'] = "";
                $resultArray['company_phone_fax'] ="";
            }
        }else{
            $resultArray['company_phone'] = "";
            $resultArray['company_phone_fax'] ="";
        }


//        $property_company_contact_id = $request->input('company_contact_id');
        if($property->management_company_id !=0){
            $property_company_contacts = Management_company_contact::where('management_company_id', $property->management_company_id)->get();
            if(count($property_company_contacts)>0) {

                $property_company_contact = Management_company_contact::where('management_company_id',$property->management_company_id)->first();
                $resultArray['company_contact_name'] = $property_company_contact->first_name." " . $property_company_contact->last_name;
            }else{
                $resultArray['company_contact_name'] = "";
            }
        }else{
            $resultArray['company_contact_name'] = "";
        }

        return response()->json(['property' =>$property, 'result' =>$resultArray] );
    }

    public function postSearchproperty(Request $request){
        $property_name = $request->input('property_name');
        $property_street = $request->input('property_street');
        $property_city = $request->input('property_city');
        $property_state = $request->input('property_state');
        $properties_query = Property::whereRaw(true);
        if($property_name !=""){
            $properties_query = $properties_query->where('property_name',$property_name);
        }
        if($property_street !=""){
            $properties_query = $properties_query->where('property_street',$property_street);
        }

        if($property_city !=""){
            $properties_query = $properties_query->where('property_city',$property_city);
        }

        if($property_state !=""){
            $properties_query = $properties_query->where('property_state',$property_state);
        }
        $properties = $properties_query->get();
        return response()->json(['response' => $properties]);
    }

    public function postGetedtiproperty(Request $request){
        $propety_id = $request->input('property_id');
        $property = Property::where('id' , $propety_id)->first();
        return response()->json(['response' =>$property]);
    }

    public function postPropertyshow(Request $request){
        $property_id = $request->input('property_id');
        $data['result'] = 'success';
        $data['type'] = 'created';
        $property=Property::where('id', $property_id)->first();
        $phones = Property_phone::where('property_id' ,$property_id)->get();
        $phoneArray = $this->getPropertyPhoneList($phones);
        $contacts = Property_contact::where('property_id' ,$property_id)->get();
        $contactArray = $this->getPropertyContactList($contacts);
        $comments = Property_comment::where('property_id' ,$property_id)->get();
        $commentArray = $this->getPropertyCommentList($comments);
        if($property->management_company_id !=0){
            $companies = Management_company::where('id' ,$property->management_company_id)->get();
        }else{
            $companies = "";
        }

        return response()->json(['response' => $data, 'property' =>$property,'phone' =>$phoneArray,'contact' =>$contactArray, 'comment' =>$commentArray, 'company' =>$companies]);
    }
    public function postProperty(request $request){

        $property_id = $request->input('property_id');
        if($property_id !=""){
            $property = Property::where('id',$property_id)->first();
            $property->update($request->all());
            $data['result'] = 'success';
            $data['type'] = 'updated';
        }else{
            $property = Property::create($request->all());
//            $property_id = 5;
//            $property = Property::where('id', $property_id)->first();
            $data['result'] = 'success';
            $data['type'] = 'created';
        }

        $phones = Property_phone::where('property_id' ,$property_id)->get();
        $phoneArray = $this->getPropertyPhoneList($phones);
        $contacts = Property_contact::where('property_id' ,$property_id)->get();
        $contactArray = $this->getPropertyContactList($contacts);
        $comments = Property_comment::where('property_id' ,$property_id)->get();
        $commentArray = $this->getPropertyCommentList($comments);
        if($property->management_company_id !=0){
            $companies = Management_company::where('id' ,$property->management_company_id)->get();
        }else{
            $companies = "";
        }
        return response()->json(['response' => $data, 'property' =>$property,'phone' =>$phoneArray,'contact' =>$contactArray, 'comment' =>$commentArray, 'company' =>$companies]);
    }
    /**** company phone ****/
    public function postPropertycompanyphone(Request $request){
        $property_phone_id = $request->input('property_phone_id');
        if($property_phone_id != ""){
            $Property_management_company_phone = Management_company_phone::where('id', $property_phone_id)->first();
            $phone = Phone::where('id',$Property_management_company_phone->phone_id)->first();
            $phone->update($request->all());
            $data['result'] = 'success';
            $data['type'] = 'updated';
        }else{
            $phone = Phone::create($request->all());
            $propertyData = array(
                'phone_id' =>$phone->id,
                'management_company_id' =>$request->input('management_company_id')
            );
            $Property_management_company_phone = Management_company_phone::create($propertyData);
            $data['result'] = 'success';
            $data['type'] = 'created';
        }
        $property_id = $request->input('property_id');
        $property_company_id = $request->input('management_company_id');
        $phones = Management_company_phone::where('management_company_id',$property_company_id)->get();
        $phoneArray = $this->getPropertyPhoneList($phones);
        return response()->json(['response' => $data, 'phone' =>$phoneArray]);
    }

    public function postPropertyeditcompanyphonedata(Request $request){
        $property_phone_id = $request->input('property_phone_id');
        $property_company_phone = Management_company_phone::where('id', $property_phone_id)->first();
        $phone = Phone::where('id',$property_company_phone->phone_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_phone' => $property_company_phone,'phone' =>$phone]);
    }

    /**** property phone *****/
    public function postPropertyphone(Request $request){
       $property_phone_id = $request->input('property_phone_id');
       if($property_phone_id != ""){
           $property_phone = Property_phone::where('id', $property_phone_id)->first();
           $phone = Phone::where('id', $property_phone->phone_id)->first();
           $phone = $phone->update($request->all());
           $data['result'] = 'success';
            $data['type'] = 'updated';

       }else{
           $phone = Phone::create($request->all());
           $propertyData =array(
               'property_id' => $request->input('property_id'),
               'phone_id' =>$phone->id
           );
           $property_phone = Property_phone::create($propertyData);
           $data['result'] = 'success';
            $data['type'] = 'created';
       }
       $property_id = $request->input('property_id');
       $phones = Property_phone::where('property_id' ,$property_id)->get();
        $phoneArray = $this->getPropertyPhoneList($phones);
       return response()->json(['response' => $data, 'phone' =>$phoneArray]);
    }
    function getPropertyPhoneList($phones){
        $phoneArrayList = array();
        if(count($phones)>0){
            foreach ($phones as $key_phone =>$phone){
                $database_phone = $phone->phone;
                $phoneType = $database_phone->property_phone_type;
                $phoneArrayList[$key_phone]['phone_type_id'] = $phoneType->name;
                $phoneArrayList[$key_phone]['id'] = $phone->id;
                $phoneArrayList[$key_phone]['area_code'] = $database_phone->area_code;
                $phoneArrayList[$key_phone]['phone_number'] = $database_phone->phone_number;
                $phoneArrayList[$key_phone]['phone_ext'] = $database_phone->phone_ext;
            }
        }
        return $phoneArrayList;
    }

    public function postPropertyeditphonedata(Request $request){
        $property_phone_id = $request->input('property_phone_id');
        $property_phone = Property_phone::where('id',$property_phone_id)->first();
        $phone = Phone::where('id',$property_phone->phone_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_phone' => $property_phone,'phone' =>$phone]);
    }
    /*****property company contact *****/
    public function postPropertycompanycontact(Request $request){
        $property_contact_id = $request->input('property_contact_id');
        if($property_contact_id != ""){
            $property_company_contact = Management_company_contact::where('id', $property_contact_id)->first();
            $property_company_contact->update($request->all());
            $data['result'] = 'success';
            $data['type'] = 'updated';
        }else{
            $property_company_contact = Management_company_contact::create($request->all());
            $data['result'] = 'success';
            $data['type'] = 'created';
        }
        $property_id = $request->input('property_id');
        $property_company_id = $request->input('management_company_id');
        $contacts = Management_company_contact::where('management_company_id',$property_company_id)->get();
        $contactArray = $this->getPropertyContactList($contacts);
        return response()->json(['response' => $data, 'contact' =>$contactArray]);
    }
    public function postPropertyeditcompanycontactdata(Request $request){
        $property_contact_id = $request->input('property_contact_id');
        $property_contact = Management_company_contact::where('id',$property_contact_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_contact' => $property_contact]);
    }

    /**** property contact *****/

    public function postPropertycontact(Request $request){
        $property_contact_id = $request->input('property_contact_id');
        if($property_contact_id != ""){
            $property_contact = Property_contact::where('id', $property_contact_id)->first();
            $property_contact->update($request->all());
            $data['result'] = 'success';
            $data['type'] = 'updated';
        }else{
            $property_contact = Property_contact::create($request->all());
            $data['result'] = 'success';
            $data['type'] = 'created';
        }
        $property_id = $request->input('property_id');
        $contacts = Property_contact::where('property_id' ,$property_id)->get();
        $contactArray = $this->getPropertyContactList($contacts);
        return response()->json(['response' => $data, 'contact' =>$contactArray]);
    }

    function getPropertyContactList($contacts){
        $contactArrayList = array();
        if(count($contacts)>0){
            foreach ($contacts as $key_contact =>$contact){
                $contactType = $contact->property_contact_type;
                $contactArrayList[$key_contact]['contact_type_id'] = $contactType->name;
                $contactArrayList[$key_contact]['id'] = $contact->id;
                $contactArrayList[$key_contact]['first_name'] = $contact->first_name;
                $contactArrayList[$key_contact]['last_name'] = $contact->last_name;
                $contactArrayList[$key_contact]['email'] = $contact->email;
            }
        }
        return $contactArrayList;
    }
    public function postPropertyeditcontactdata(Request $request){
        $property_contact_id = $request->input('property_contact_id');
        $property_contact = Property_contact::where('id',$property_contact_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_contact' => $property_contact]);
    }

    /*** property comment ****/

    public function postPropertycomment(Request $request){
        $property_comment_id = $request->input('property_comment_id');
        if($property_comment_id !=""){
            $property_comment = Property_comment::where('id', $property_comment_id)->first();
            $property_comment->comment = $request->input('comment');
            $property_comment->save();
            $data['result'] = 'success';
            $data['type'] = 'updated';
        }else{
            $property_comment = new Property_comment();
            $property_comment->comment = $request->input('comment');
            $property_comment->creator_id = Auth::user()->id;
            $property_comment->property_id = $request->input('property_id');
            $property_comment->save();
            $data['result'] = 'success';
            $data['type'] = 'created';
        }
        $property_id = $request->input('property_id');
        $comments = Property_comment::where('property_id' ,$property_id)->get();
        $commentArray = $this->getPropertyCommentList($comments);
        return response()->json(['response' => $data, 'comment' =>$commentArray]);
    }
    function getPropertyCommentList($comments){
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
    public function postPropertyeditcommentdata(Request $request){
        $property_comment_id = $request->input('property_comment_id');
        $property_comment = Property_comment::where('id',$property_comment_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_comment' => $property_comment]);
    }

    public function postPropertycompany(Request $request){
        $property_company_id = $request->input('property_company_id');
        $property_id = $request->input('property_id');
        $property = Property::where('id', $property_id)->first();
        if($property_company_id !=""){
            $property_company = Management_company::where('id',$property->management_company_id)->first();
            $property_company->update($request->all());
            $data['result'] = 'success';
            $data['type'] = 'updated';
        }else{
            $company = Management_company::create($request->all());
            $property->management_company_id = $company->id;
            $property->save();
            $data['result'] = 'success';
            $data['type'] = 'created';
        }
        if($property->management_company_id !=0){
            $companies = Management_company::where('id' ,$property->management_company_id)->get();
        }else{
            $companies = "";
        }

        return response()->json(['response' => $data, 'company' =>$companies]);
    }

    public function postPropertyeditcompanydata(Request $request){
        $property_company_id = $request->input('property_company_id');
        $property_company = Management_company::where('id',$property_company_id)->first();
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_company' => $property_company]);
    }

    public function postPropertyassigncompanydata(Request $request){
        $property_company_id = $request->input('property_company_id');
        $property_company = Management_company::where('id',$property_company_id)->first();
        $property = Property::where('management_company_id', $property_company_id)->first();
        $property_id = $property->property_id;
        $phones = Management_company_phone::where('management_company_id',$property_company_id)->get();
        $phoneArray = $this->getPropertyPhoneList($phones);
        $contacts = Management_company_contact::where('management_company_id',$property_company_id)->get();
        $contactArray = $this->getPropertyContactList($contacts);
        $data['result'] = 'success';
        return response()->json(['response' =>$data, 'property_company' => $property_company, 'phone' =>$phoneArray,'contact' =>$contactArray]);
    }


}
