<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SideBanner;
use Session;
use DB;
use Illuminate\Support\Facades\Redirect;


class SideBannerController extends Controller
{

    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }

    public function delete_sidebanner($banner_id){
        $this->AuthLogin();
        DB::table('tbl_sidebanner')->where('banner_id',$banner_id)->delete();
        Session::put('message','Xóa side banner thành công');
        return Redirect::to('manage-side-banner');
    }

    public function unactive_sidebanner($banner_id){
        $this->AuthLogin();
        DB::table('tbl_sidebanner')->where('banner_id',$banner_id)->update(['banner_status'=>0]);
        Session::put('message','Hủy kích hoạt slider thành công');
        return Redirect::to('manage-side-banner');

    }
    public function active_sidebanner($banner_id){
        $this->AuthLogin();
        DB::table('tbl_sidebanner')->where('banner_id',$banner_id)->update(['banner_status'=>1]);
        Session::put('message','Đã kích hoạt slider thành công');
        return Redirect::to('manage-side-banner');

    }

    public function manage_sidebanner(){
        $this->AuthLogin();
        $all_sidebanner = SideBanner::orderby('banner_id','DESC')->get();
        return view('admin.sidebanner.list_sidebanner')->with(compact('all_sidebanner'));
    }

    public function add_sidebanner() {
        $this->AuthLogin();
        return view('admin.sidebanner.add_sidebanner'); 
    }

    public function insert_sidebanner(Request $request) {
        $this->AuthLogin();

   		$data = $request->all(); //lay tat ca du lieu tu add_sidebanner
       	$get_image = request('banner_image');  //banner_image la name trong add_sidebanner
      
        if($get_image){
            $get_name_image = $get_image->getClientOriginalName();
            // getClientOriginalName la ham lay tenfile hinh
            $name_image = current(explode('.',$get_name_image)); //ham explose chia ten file anh thanh ten + . + jpg/png
            // neu dung current thi lay phan ten hoac end de lay phan duoi jpg/png 


            $new_image =  $name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();
            //ở đây tên file hình được ghép thêm với số ngẫu nhiên (rand(0,99)) để tránh trùng lặp
            //getClientOriginalExtension la ham lay duoi cua file hinh (PNG/JPG)


            $get_image->move('public/uploads/slider', $new_image);
            // sau đó lấy hình ảnh up load lên đưa vào thư mục ở trên


            $sidebanner = new SideBanner();
            $sidebanner->banner_name = $data['banner_name'];
            $sidebanner->banner_desc = $data['banner_desc'];
            $sidebanner->banner_status = $data['banner_status'];
            $sidebanner->banner_image = $new_image;
           	$sidebanner->save();
            Session::put('message','Thêm side banner thành công');
            return Redirect::to('add-side-banner');
        }else{
        	Session::put('message','Vui lòng thêm hình ảnh');
    		return Redirect::to('add-side-banner');
        }
    }
}
