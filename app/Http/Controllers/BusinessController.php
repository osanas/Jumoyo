<?php

namespace App\Http\Controllers;

use App\Gateways\BusinessGateway;
use App\Http\Requests\ServiceEditRequest;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessRequest;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\ServiceRequest;
use App\Http\Requests\PostRequest;
use App\Http\Requests\EditPostRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\BusinessEditRequest;

class BusinessController extends Controller
{
    private $businessGateway;

    public function __construct() {
        $this->businessGateway = new BusinessGateway();
    }

    public function register(BusinessRequest $request) {
        $name = $request->input('name');
        $address = $request->input('address');
        $city = $request->input('city');
        $postalCode = $request->input('postal');
        $country = $request->input('country');
        $state = $request->input('state');

        if($this->businessGateway->register($name, $address, $city, $postalCode, $country, $state))
            return response('Success.', 200);
        else
            return abort(400, "An error occurred during the registration.");
    }

    public function getBusinessInfo(){
        $business = $this->businessGateway->getBusiness();
        //dd($business[0]);
        return view('pages.business.businessinfo', ['business' => $business, 'page' => 'BUSINESS INFO']);
    }

    public function updateBusiness(BusinessEditRequest $request) {
        $id = $this->businessGateway->getBusinessID();
        $name = $request->input('name');
        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $address = $request->input('address');
        $city = $request->input('city');
        $postal_code = $request->input('postal_code');
        $state = $request->input('state');
        $country = $request->input('country');
        $facebook = $request->input('facebook');
        $twitter = $request->input('twitter');
        $instagram = $request->input('instagram');

        if($this->businessGateway->editBusiness($id, $name, $email, $mobile, $address, $city, $postal_code, $state, $country, $facebook, $twitter, $instagram))
            return response('Success.', 200);
        else
            return abort(400, "An error occurred during the update.");
    }

    public function post(PostRequest $request) {
        $title = $request->input('title');
        $text = $request->input('text');
        $url = $request->input('url');
        $service = $request->input('service');
        $businessID = $this->businessGateway->getBusinessID();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('img/business/' . $businessID . '/posts/');
            $filePath = 'img/business/' . $businessID . '/posts/';
            if(!file_exists($destinationPath)){
                mkdir($destinationPath,755,true);
                $image->move($destinationPath,$destinationPath.$imageName);
                $path = $filePath.$imageName;
            }
            else {
                $image->move($destinationPath,$destinationPath.$imageName);
                $path = $filePath.$imageName;
            }
        }
        else {
            $path = null;
        }

        $image = $path;

        if($this->businessGateway->addPost($title, $service, $text, $url, $image)) {
            return redirect('/business/post');
        }
        else
            return abort(400, "An error occurred during the process.");
    }

    public function getPost() {
        $data = $this->businessGateway->getPost();
        $serviceName = $this->businessGateway->getUserService();
        //dd($data);
        return view('pages.business.post', ['data' => $data, 'serviceName' => $serviceName, 'page' => 'POST']);
    }

    public function deletePost($id) {
        $this->businessGateway->removePost($id);

        return redirect('/business/post');
    }

    public function editPost(EditPostRequest $request) {
        $id = $request->input('postId');
        $title = $request->input('editTitle');
        $text = $request->input('editText');
        $select = $request->input('editSelect');

        if($this->businessGateway->editPost($id, $title, $text, $select)) {
            return response('Success.', 200);
        }
        else
            return abort(400, "An error occurred during the process.");
    }

    //Service related stuff starts here

    public function getMyService($id) {
        $data = $this->businessGateway->getMyService($id);

        return view('pages.business.editservice', ['data' => $data,'page' => 'SERVICES']);
    }

    public function getMyServices() {
        $data = $this->businessGateway->getMyServices();

        return view('pages.business.myservices', ['data' => $data, 'page' => 'SERVICES']);
    }

    public function removeMyService($id) {
        $this->businessGateway->removeMyService($id);

        return redirect('/business/myservices');
    }

    public function editMyService(ServiceEditRequest $request)
    {
        $serviceId = $request->get('serviceId'); // Test
        $category = $request->input('category');
        $name = $request->input('name');
        $price = $request->input('price');
        $priceHourly = $request->input('priceHourly');
        $description = $request->input('desc');

        if($this->businessGateway->editMyService($serviceId, $category, $name, $price, $priceHourly, $description))
            return response('Success.', 200);
        else
            return abort(400, "An error occurred during the update.");
    }

    public function services(ServiceRequest $request) {
        $category = $request->input('category');
        $name = $request->input('name');
        $price = $request->input('price');
        $priceHourly = $request->input('priceHourly');
        $description = $request->input('desc');
        $businessHours = json_decode($request->input('businessHour'));

        $value = $this->businessGateway->addService($category, $name, $price, $priceHourly, $description, $businessHours);
        if($value) {
            $this->businessGateway->addPortfolio(null, null, $value);
            return response('Success.', 200);
        }
        else
            return abort(400, "An error occurred during the registration.");
    }

    public function displayContacts(){
        $contacts = $this->businessGateway->getContacts();
        //dd($contacts);
        return view('pages.business.contact', ['contacts' => $contacts, 'page' => 'CONTACT']);
    }

    public function addContact(ContactRequest $request){
        $businessID = $this->businessGateway->getBusinessID();
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $address = $request->input('address');
        if ($request->hasFile('image')) {
            $imageTemp = $request->file('image');
            $imgName = time() . '.' . $imageTemp->getClientOriginalExtension();
            $destinationPath = public_path('img/business/' . $businessID . '/contacts/');
            $filePath = 'img/business/' . $businessID . '/contacts/';
            if(!file_exists($destinationPath)){
                mkdir($destinationPath,755, true );
                $imageTemp->move($destinationPath,$destinationPath.$imgName);
                $url = $filePath.$imgName;
            }else{
                $imageTemp->move($destinationPath,$destinationPath.$imgName);
                $url = $filePath.$imgName;
            }
        } else {
            $url = null;
        }
        $image = $url;
        if($this->businessGateway->addContact($name, $phone, $email, $address, $businessID, $image)){
            return redirect('business/contact');
        } else {
            return abort(400, "An error occurred while adding contact.");
        }
    }

    public function editContact(ContactRequest $request){
        $businessID = $this->businessGateway->getBusinessID();
        $contactID = $request->input('contactID');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $address = $request->input('address');
        $image = $request->hasFile('image');
        if($image){
            $imageTemp = $request->file('image');
            $imgName = time() . '.' . $imageTemp->getClientOriginalExtension();
            $destinationPath = public_path('img/business/' . $businessID . '/contacts/');
            $filePath = 'img/business/' . $businessID . '/contacts/';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 755, true);
                $imageTemp->move($destinationPath, $destinationPath . $imgName);
                $url = $filePath . $imgName;
            } else {
                $imageTemp->move($destinationPath, $destinationPath . $imgName);
                $url = $filePath . $imgName;
            }
            $image = $url;
        }
        if($this->businessGateway->changeContact($contactID, $name, $phone, $email, $address, $image)){
            $request->Session()->flash('success_msg','Changes are made!');
            return redirect('business/contact');
        } else if($this->businessGateway->changeContact($contactID, $name, $phone, $email, $address, $image)===0){
            $request->Session()->flash('error_msg','Nothing has been changed!');
            return redirect('business/contact');
        } else{
            return abort(400, "An error occurred while editing contact.");
        }
    }

    public function removeContact($contactID){
        if($this->businessGateway->deleteContact($contactID)){
            return redirect('business/contact');
        } else {
            return abort(400, "An error occurred while removing contact.");
        }
    }

    public function getAllReviews() {
        $serviceName = $this->businessGateway->getUserService();
        $businessID = $this->businessGateway->getBusinessID();
        $reviews = $this->businessGateway->getAllReviews($businessID);

        return view('pages.business.reviews', ['serviceName' => $serviceName, 'reviews' => $reviews, 'page' => 'REVIEWS']);
    }

    public function getReviews(Request $request)
    {
        $id = $request->input('id');
        $reviews = $this->businessGateway->getReview($id);

        return json_encode((array)$reviews);
    }

    public function addPortfolio(PortfolioRequest $request) {
        $businessID = $this->businessGateway->getBusinessID();
        $serviceID = $request->input('service');
        $link = $request->input('link');

        if ($request->hasFile('image')) {
            $imageTemp = $request->file('image');
            $imgName = time() . '.' . $imageTemp->getClientOriginalExtension();
            $destinationPath = public_path('img/business/' . $businessID . '/'.$serviceID.'/');
            $filePath = 'img/business/' . $businessID . '/'.$serviceID.'/';
            if(!file_exists($destinationPath)){
                mkdir($destinationPath,755, true );
                $imageTemp->move($destinationPath,$destinationPath.$imgName);
                $url = $filePath.$imgName;
            }else{
                $imageTemp->move($destinationPath,$destinationPath.$imgName);
                $url = $filePath.$imgName;
            }
        } else {
            $url = null;
        }
        $image = $url;

        $this->businessGateway->addPortfolio($image, $link, $serviceID);

        return response('success', 200);
    }

    public function getPortfolio(Request $request) {
        $businessID = $this->businessGateway->getBusinessID();
        $data = $this->businessGateway->getAllImages($businessID);
        $serviceName = $this->businessGateway->getUserService();

        for($i = 0; $i < count($data); $i++) {
            $data[$i]->services_id = $this->businessGateway->getService($data[$i]->services_id)->name;
            if($data[$i]->url) {
                $data[$i]->url = explode("=", $data[$i]->url)[1];
            }
            if(!$data[$i]->name && !$data[$i]->url) {
                array_splice($data, $i, 1);
                $i = $i - 1;
            }
        }

        return view('pages.business.portfolio', ['data' => $data, 'serviceName' => $serviceName, 'page' => 'PORTFOLIO']);
    }

    public function deletePortfolio(Request $request) {
        $id = $request->input('id');

        if($this->businessGateway->deletePortfolio($id)) {
            return redirect('/business/portfolio');
        }
        else{
            return abort(400, "An error occurred while removing contact.");
        }
    }

    public function getBookings(Request $request) {
        $id = $request->input('id');

        return json_encode($this->businessGateway->getBookings($id));
    }

    public function getBookingsFeed(Request $r) {
        if (!$r->has('start') || !$r->has('end')) {
            return abort(400, 'Wrong parameters.');
        } 
        $start = $r->input('start');
        $end = $r->input('end');
        $businessid = $this->businessGateway->getBusinessID();
        return response()->json($this->businessGateway->getBookingsFeed($businessid, $start, $end));
    }

    public function confirmBooking($bookingid) {
        $this->businessGateway->changeBookingConfirmation($bookingid, 1);
        return response('success', 200);
    }

    public function deleteBooking($bookingid) {
        $this->businessGateway->deleteBooking($bookingid);
        return response('success', 200);
    }
}