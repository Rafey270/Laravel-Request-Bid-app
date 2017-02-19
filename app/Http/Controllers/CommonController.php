<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Bid_status;

use App\Source;

use App\Property_type;

use App\Mail_to_type;

use App\Phone_type;

use Bican\Roles\Models\Role;

use DB;

use App\Contact_type;

use App\Bid_type;

use App\Bid_template;

use App\File_type;
class CommonController extends Controller
{
    public function getSources(Request $request){
        $sources = Source::all();

        return Response()->json(['response' =>$sources]);
    }
    public function getBidstatus(Request $request){
        $status = Bid_status::all();

        return Response()->json(['response' =>$status]);
    }
    public function getEstimator(Request $request){
        $estimator_role = Role::where('name','Estimator')->first();
        $estimator_users = DB::table('role_user')->where('role_id',$estimator_role->id)->get();
        $list = array();
        if(count($estimator_users)>0){
            foreach ($estimator_users as $key_user =>$estimator_user){
                $list[$key_user]['id'] = $estimator_user->user_id;
                $user = User::find($estimator_user->user_id);
                $list[$key_user]['name'] = $user->name;
            }
        }
        return Response()->json(['response' =>$list]);
    }
    public function getPropertytype(){
        $propertyTypes = Property_type::all();
        return Response()->json(['response' =>$propertyTypes]);
    }

    public function getMailtotype(){
        $mailToTypes = Mail_to_type::all();
        return Response()->json(['response' =>$mailToTypes]);
    }

    public  function getPhonetype(){
        $phoneTypes = Phone_type::all();
        return Response()->json(['response' =>$phoneTypes]);
    }
    public function getContacttype(){
        $contactTypes =Contact_type::all();
        return Response()->json(['response' =>$contactTypes]);
    }

    public function getBidtype(){
        $bidTypes = Bid_type::all();
        return Response()->json(['response' =>$bidTypes]);
    }

    public function getBidtemplate(){
        $bidTemplates = Bid_template::all();
        return Response()->json(['response' =>$bidTemplates]);
    }

    public function getFiletype(){
        $filetypes = File_type::all();
        return Response()->json(['response' =>$filetypes]);
    }
}
