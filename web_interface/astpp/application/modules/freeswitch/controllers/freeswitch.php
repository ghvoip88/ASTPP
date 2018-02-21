<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
class Freeswitch extends MX_Controller {

    function Freeswitch() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("freeswitch_form");
        $this->load->library('astpp/form');
        $this->load->library('freeswitch_lib');
        $this->load->model('freeswitch_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function fssipdevices_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add sip device';
        if ($type != "") {
            $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($type), '');
        } else {
            $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields(), '');
        }
//         print_r($data);exit;
        $this->load->view('view_freeswitch_add_edit', $data);
    }
    function customer_fssipdevices_add($accountid) {
        $data['page_title'] = 'Add Sip Device';
        $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($accountid),"");
        $this->load->view('view_freeswitch_add_edit', $data);
    }

    function fssipdevices_edit($edit_id = '') {
        $data['page_title'] = 'Edit sip device';
        $where = array('id' => $edit_id);
        $account = $this->freeswitch_model->get_edited_data($edit_id);
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields($edit_id), $account);
        $this->load->view('view_freeswitch_add_edit', $data);
    }

    function customer_fssipdevices_edit($edit_id, $accountid) {
        $data['page_title'] = 'Edit sip device';
        $where = array('id' => $edit_id);
        $account = $this->freeswitch_model->get_edited_data($edit_id);
        $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($accountid,$edit_id), $account);
        $this->load->view('view_freeswitch_add_edit', $data);
    }
    
    function fsgateway_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fsgateway_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'freeswitch/fsgateway/');
        }
    }
    function fssipprofile_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fssipprofile_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'freeswitch/fssipprofile/');
        }
    }
    function fssipdevices_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }
    function fsgateway_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }
    function fssipprofile_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }
    function fssipdevices_save($user_flg = false) {
//         echo 'dsvd';exit;
        $add_array = $this->input->post();
//         print_r($add_array);exit;
        if (!$user_flg) {
            $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields($add_array['id']), $add_array);
        } else {
            $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($add_array["accountcode"],$add_array['id']),  $add_array);
        }
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_freeswith($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "SIP Devices Updated Successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_freeswith($add_array);
                echo json_encode(array("SUCCESS"=> "SIP Devices Added Successfully!"));
                exit;
            }
        }
    }
    function customer_fssipdevices_save($user_flg = false) {
        $add_array = $this->input->post();
       
        if (!$user_flg) {
            $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields(), $add_array);
        } else {
            $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($add_array["accountcode"],$add_array['id']), $add_array);
        }
//         echo "<pre>";print_r($add_array);exit;
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_freeswith($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "SIP Devices Updated Successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_freeswith($add_array);
                echo json_encode(array("SUCCESS"=> "SIP Devices Added Successfully!"));
                exit;
            }
        }
    }
    

    function user_fssipdevices_save($user_flg = false) {
        
        $add_array = $this->input->post();
//        print_r($add_array);
//        exit;
        $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($add_array["accountcode"],$add_array['id']), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_freeswith($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "SIP Devices Updated Successfully!"));
                exit;
            }
        }else{
            $data['page_title'] = 'Create Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
 			$sip_profile_id=$this->common->get_field_name('id','sip_profiles',array('name'=>'default'));
	//	$add_array['sip_profile_id']=$sip_profile_id;
                $this->freeswitch_model->add_freeswith($add_array);
                echo json_encode(array("SUCCESS"=> "SIP Devices Added Successfully!"));
                exit;
            }
        }
    }

    function fssipdevices_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            
//             print_r($action);
//             exit;
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fssipdevices_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'freeswitch/fssipdevices/');
        }
    }


    function fssipdevices() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'SIP Devices';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_system_list_for_admin();
        $data["grid_buttons"] = $this->freeswitch_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->freeswitch_form->get_sipdevice_search_form());
        $this->load->view('view_freeswitch_sip_devices_list', $data);
    }

    function fssipdevices_json() {
        $json_data = array();
        $count_all = $this->freeswitch_model->fs_retrieve_sip_user(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->freeswitch_model->fs_retrieve_sip_user(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->freeswitch_form->build_system_list_for_admin());
        foreach ($query as $key => $value) {
            $json_data['rows'][] = array('cell' => array(
//                     $value['id'],
		    '<input type="checkbox" name="chkAll" id=' . $value['id'] . ' class="ace chkRefNos" onclick="clickchkbox(' . $value['id'] . ')" value=' . $value['id'] . '><lable class="lbl"></lable>',
                    $value['username'],
                    $value['password'],
                    $this->common->get_field_name('name', '`sip_profiles', array('id' => $value['sip_profile_id'])),
//                     $this->common->get_field_name('number', 'accounts', array('id' => $value['accountid'])),
                    $this->common->get_field_name_coma_new('first_name,last_name,number', 'accounts', array('0' => $value['accountid'])),
                    $value['effective_caller_id_name'],
                    $value['effective_caller_id_number'],
                     $this->common->get_status('status', '',$value['status']),
//                     $value['context'],
                    $this->get_action_buttons_fssipdevices($value['id'])
                    ));
        }

        echo json_encode($json_data);
    }
    
    function fssipdevices_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("sip_devices");
    }

    function user_fssipdevices_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("sip_devices");
    }

    function customer_fssipdevices_json($accountid) {
        $json_data = array();
        $count_all = $this->freeswitch_model->get_sipdevices_list(false, $accountid);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $devices_result = array();
        $query = $this->freeswitch_model->get_sipdevices_list(true, $accountid, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        foreach ($query as $key => $value) {
            $json_data['rows'][] = array('cell' => array(
		    '<input type="checkbox" name="chkAll" id="'.$value['id'].'" class="ace chkRefNos" onclick="clickchkbox('.$value['id'].')" value=' .$value['id'].'><lable class="lbl"></lable>',
                    $value['username'],
                    $value['password'],
                    $this->common->get_field_name('name', '`sip_profiles', array('id' => $value['sip_profile_id'])),
                    $value['effective_caller_id_name'],
                    $value['effective_caller_id_number'],
                    $this->common->get_status('status', '',$value['status']),
//                     $value['context'],
                    $this->get_action_fssipdevices_buttons($value['id'], $value['accountid'])
                    ));
        }
        echo json_encode($json_data);
    }

    function get_action_fssipdevices_buttons($id, $accountid) {
        $ret_url = '';
        if ($this->session->userdata("logintype") == '0'||$this->session->userdata("logintype") == '3') {
            $ret_url = '<a href="'. base_url() .'user/user_fssipdevices_action/edit/' . $id . '/' . $accountid . '/" class="btn btn-royelblue btn-sm"  rel="facebox" title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
            $ret_url .= '<a href="'. base_url() .'user/user_fssipdevices_action/delete/' . $id . '/' . $accountid . '/" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();">&nbsp;<i class="fa fa-trash fa-fw"></i></a>';
        } else {
            $ret_url = '<a href="'. base_url() .'accounts/customer_fssipdevices_action/edit/' . $id . '/' . $accountid . '/" class="btn btn-royelblue btn-sm"  rel="facebox" title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
            $ret_url .= '<a href="'. base_url() .'accounts/customer_fssipdevices_action/delete/' . $id . '/' . $accountid . '/" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();">&nbsp;<i class="fa fa-trash fa-fw"></i></a>';
        }
        return $ret_url;
    }

    function fssipdevices_delete($id) {
        $this->freeswitch_model->delete_freeswith_devices($id);
        $this->session->set_flashdata('astpp_notification', 'Sip Devices Removed Successfully!');
        redirect(base_url() . 'freeswitch/fssipdevices/');
        exit;
    }

    function get_action_buttons_fssipdevices($id) {

        $ret_url = '';
        $ret_url = '<a href="'. base_url() .'freeswitch/fssipdevices_edit/' . $id . '/" class="btn btn-royelblue btn-sm"  rel="facebox" title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
        $ret_url .= '<a href="'. base_url() .'freeswitch/fssipdevices_delete/' . $id . '/" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();">&nbsp;<i class="fa fa-trash fa-fw"></i></a>';
        return $ret_url;
    }

    function livecall_report() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Live Call Report';
//	$data['search_flag'] = true;
//	$data['form_search']=$this->form->build_serach_form($this->freeswitch_form->get_freeswith_search_form());
        $this->load->view('view_fs_livecall_report', $data);
    }

    function livecall_report_json() {
/*        $fp = $this->freeswitch_lib->event_socket_create("127.0.0.1","8021","ClueCon");
        $cmd = "api show channels";
        $response_arr = array();
        $response_arr[] = $this->freeswitch_lib->event_socket_request($fp, $cmd);*/

        $command = "api show channels";
        $response = $this->freeswitch_model->reload_live_freeswitch($command);
        $calls = array();
        $calls_final = array();
        $data_header = array();
        $k = 0;
//         foreach($response_arr as $resp_key=>$response){
            $data = explode("\n",$response);
            for ($i = 0; $i < count($data) - 2; $i++) {
                if (trim($data[$i]) != '') {
                    if (count($data_header) ==0 ) {
                        $data_header = explode(",", $data[$i]);
                    } else {
                        $data_call = explode(",", $data[$i]);
                        for ($j = 0; $j < count($data_call); $j++) {
                            $calls[$k][@$data_header[$j]] = @$data_call[$j];
                            $calls_final[@$calls[$k]['uuid']] = @$calls[$k];
                        }
                        $k++;
                    }
                }
            }
//         }
        $json_data = array();
        $count = 0;
//	echo "<pre>";		print_r($calls);exit;
        //for($i=0;$i<count($calls)-1;$i++)
        foreach ($calls as $key => $value) {
            if (isset($value['state']) && $value['state'] == 'CS_EXCHANGE_MEDIA') {
                $calls[$i]['application'] = $calls_final[$value['call_uuid']]['application'];
                $calls[$i]['application_data'] = $calls_final[$value['call_uuid']]['application_data'];
                $json_data['rows'][] = array('cell' => array(
                        $value['created'],
                        $value['cid_name'],
                        $value['cid_num'],
                        $value['ip_addr'],
                        $value['dest'],
                        $calls[$i]['application_data'],
                        $value['read_codec'],
                        $value['write_codec'],
                        $value['callstate'],
                        date("H:i:s", strtotime(date("Y-m-d H:i:s")) - $value['created_epoch'])
//			gmdate("H:i:s", strtotime(date("Y-m-d H:i:s")) - strtotime($value['created']))
                        ));
                $count++;
            } else {
                unset($calls[$i]);
            }
        }
	$json_data['page'] = 1;
        $json_data['total'] = $count;
//         fclose($fp);
        echo json_encode($json_data);
    }

    function fsgateway() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Gateways';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_fsgateway_list_for_admin();
        $data["grid_buttons"] = $this->freeswitch_form->build_fdgateway_grid_buttons();
      	$data['form_search']=$this->form->build_serach_form($this->freeswitch_form->get_gateway_search_form());
        $this->load->view('view_fsgateway_list', $data);
    }

    function fsgateway_json() {
        $json_data = array();

        $count_all = $this->freeswitch_model->get_gateway_list(false);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $gateway_data = array();
        $query = $this->freeswitch_model->get_gateway_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $gateway_result = array();
        if ($query->num_rows > 0) {
            $query = $query->result_array();
            foreach ($query as $key => $query_value) {
$gateway_data=array();
$tmp=null;
                foreach ($query_value as $gateway_key => $gateway_val) {
                    if ($gateway_key != "gateway_data") {
                        $gateway_data[$gateway_key] = $gateway_val;
                    } else {
                        $tmp = (array) json_decode($gateway_val);
                    }
                }
                        $gateway_result[$key] = array_merge($gateway_data, $tmp);
            }
        }

        $grid_fields = json_decode($this->freeswitch_form->build_fsgateway_list_for_admin());
        $json_data['rows'] = $this->form->build_json_grid($gateway_result, $grid_fields);
        echo json_encode($json_data);
    }

    function fsgateway_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Gateway';
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_gateway_form_fields(), '');
        $this->load->view('view_fsgateway_add', $data);
    }

    function fsgateway_edit($edit_id = '') {
        $data['page_title'] = ' Edit Gateway';
        $where = array('id' => $edit_id);
        $query = $this->db_model->getSelect("*", "gateways", $where);
        $query = $query->result_array();
        $gateway_result = array();
//print_r($query);
        foreach ($query as $key => $query_value) {
            foreach ($query_value as $gateway_key => $gatewau_val) {
  	        $gateway_data["status"] = isset($query_value["status"])?$query_value["status"]:"";
                if ($gateway_key != "gateway_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                }else if($gateway_key == "status") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
            }
        }

        $data['form'] = $this->form->build_form($this->freeswitch_form->get_gateway_form_fields(), $gateway_result);
        $this->load->view('view_fsgateway_add', $data);
    }

    function fsgateway_save() {
        $gateway_data = $this->input->post();
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_gateway_form_fields(), $gateway_data);
        $insert_arr = array();
        $gateway_arr = array();
        foreach ($gateway_data as $key => $gateway_value) {
            if ($gateway_value != "") {
                if ($key == "sip_profile_id") {
                    $insert_arr['sip_profile_id'] = $gateway_data["sip_profile_id"];
                } else if ($key == "name") {
                    $insert_arr['name'] = $gateway_data["name"];
                } else if ($key == "sip_profile_id") {
                    $insert_arr['sip_profile_id'] = $gateway_data["sip_profile_id"];
                }else if($key == "status") {
                    $insert_arr[$key] = $gateway_data["status"];
                }  else {
                    if ($key != "id") {
                        $gateway_arr[$key] = $gateway_value;
                    }
                }
            }
        }

        $insert_arr["gateway_data"] = json_encode($gateway_arr);

        if ($gateway_data['id'] != '') {
            $data['page_title'] = 'Edit Gateway Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
		if ( preg_match('/\s/',$insert_arr['name']) )
		{
		  echo json_encode(array("name_error"=> "Gateway name must not have any space."));
		  exit;
		}
                $update = $this->db->update("gateways", $insert_arr, array('id' => $gateway_data['id']));
                if ($update) {
                    $profile_name = $this->common->get_field_name('name', 'sip_profiles', $insert_arr['sip_profile_id']);
		    $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $insert_arr['sip_profile_id']);
                    $cmd = "api sofia profile ".$profile_name." killgw '".$insert_arr['name']."' ";
                    $this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);

                    $cmd2 = "api sofia profile " . $profile_name . " rescan reloadacl reloadxml";
                    $this->freeswitch_model->reload_freeswitch($cmd2,$sip_ip);
                }
                echo json_encode(array("SUCCESS"=> $insert_arr['name']." Gateway Updated Successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Gateway Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
		if ( preg_match('/\s/',$insert_arr['name']) )
		{
		  echo json_encode(array("name_error"=> "Gateway name must not have any space."));
		  exit;
		}
        	$insert_arr['created_date']=gmdate('Y-m-d H:i:s'); 
                $insert = $this->db->insert("gateways", $insert_arr);
                if ($insert) {
                    $profile_name = $this->common->get_field_name('name', 'sip_profiles', $insert_arr['sip_profile_id']);
		    $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $insert_arr['sip_profile_id']);
                    $cmd = "api sofia profile " . $profile_name . " rescan reloadacl reloadxml";
                    $this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);
                }
                echo json_encode(array("SUCCESS"=> $insert_arr['name']." Gateway Added Successfully!"));
                exit;
            }
        }
    }

    function fsgateway_delete($gateway_id) {
        $delete = $this->db_model->delete("gateways", array("id" => $gateway_id));
        if ($delete) {
            $profile_id = $this->common->get_field_name('sip_profile_id', 'gateways', $gateway_id);
            $profile_name = $this->common->get_field_name('name', 'sip_profiles', $profile_id);
	    $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $profile_id);
            $gateway_name = $this->common->get_field_name('name', 'gateways', $gateway_id);
            $cmd = "api sofia profile " . $profile_name . " killgw " . $gateway_name . " reloadxml";
            $this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);
        }

        $this->session->set_flashdata('astpp_notification', 'Gateway Removed Successfully!');
        redirect(base_url() . 'freeswitch/fsgateway/');
    }

    function fsgateway_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("gateways");
    }

    function fssipprofile() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Sip Profile';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_fssipprofile_list_for_admin();
        $data["grid_buttons"] = $this->freeswitch_form->build_fssipprofile_grid_buttons();
	$data['form_search']=$this->form->build_serach_form($this->freeswitch_form->get_sipprofile_search_form());
	$data['button_name']="Add Setting";
	//$data['form_search'] = $this->form->build_serach_form($this->trunk_form->get_trunk_search_form());
        $this->load->view('view_fssipprofile_list', $data);
    }

    function fssipprofile_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("sip_profiles");
    }

    function fssipprofile_json() {
        $json_data = array();

        $count_all = $this->freeswitch_model->get_sipprofile_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $gateway_data = array();
        $query = $this->freeswitch_model->get_sipprofile_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->freeswitch_form->build_fssipprofile_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }
    function fssipprofile_params_json($edited_id)
    {
	$json_data = array();

        $gateway_data = array();

	$where = array('id' => $edited_id);
	
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        $query = $query->result_array();
        
        $gateway_result = array();
        $i=0;
        foreach ($query as $key => $query_value) {
	  

            foreach ($query_value as $gateway_key => $gatewau_val) {
//             echo $gateway_key."---";
	      if($gateway_key != 'id' && $gateway_key != 'name' && $gateway_key != 'sip_ip' && $gateway_key != 'sip_port'){
		
                if ($gateway_key != "profile_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
	}
      }
    }
         $paging_data = $this->form->load_grid_config(count($gateway_result), $_GET['rp'], $_GET['page']);
         $json_data = $paging_data["json_paging"];
    
	  foreach ($gateway_result as $key => $value) {
//       if($key != 'id' || $key != 'name' || $key != 'sip_ip' ||$key != 'sip_port'){
	  $json_data['rows'][] = array('cell' => array(
	      $key,
	      $value,
	      array('<a href="/freeswitch/fssipprofile_edit/'.$edited_id.'/edit/' . $key .'/" class="btn btn-royelblue btn-sm"  title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;'
	      ,'<a href="/freeswitch/fssipprofile_delete_params/'.$edited_id.'/' . $key .'/" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();">&nbsp;<i class="fa fa-trash fa-fw"></i></a>')
	      ));
  }
        echo json_encode($json_data);
    }
     function fssipprofile_action($button_name,$id) {
        
        
//         echo 'sadvdsv';exit;
//         print_r($button_name);exit;
        $where = array('id' => $id);
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        $query = $query->result_array();
//         echo "<pre>";print_r($query);exit;
        $where = array('sip_profile_id' => $id);

//          echo "<pre>";print_r($gwdata);exit;
        if($button_name == "start")
        { 
           
           $cmd = "api sofia profile " . trim($query[0]['name']) ." start";
	   
        }
        elseif($button_name == "stop")
        {
            $cmd= "api sofia profile stop";
        }
        elseif($button_name == "reload")
        {
            $cmd = "api reloadxml";
        }
        elseif($button_name == "rescan")
        {
            $cmd = "api sofia profile " . trim($query[0]['name']) . " rescan";
            
        }
        
        $this->freeswitch_model->reload_freeswitch($cmd);
	redirect(base_url() . 'freeswitch/fssipprofile/');   
    }
     function fssipprofile_add($add='') {
     
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add Sip Profile';
//         $data['form'] = $this->form->build_form($this->freeswitch_form->get_sipprofile_form_fields(), '');
        $sipprofile_data = $this->input->post();
         $sipprofile_data['status']=$sipprofile_data['sipstatus'];
        $data['button_name']="Add Setting";
        if($add == 'add')
        {
          
	  unset($sipprofile_data['action']);
	   unset($sipprofile_data['sipstatus']);
	  $insert_data=$sipprofile_data;
	  
	  if($sipprofile_data['name'] == '' || $sipprofile_data['sip_ip'] =='' || $sipprofile_data['sip_port'] =='')
	  {
	      $this->session->set_flashdata('astpp_notification', 'Please enter All profile value!');
	      redirect(base_url() . 'freeswitch/fssipprofile_add/');
	      exit;
	  }
	  if(preg_match('/\s/',$sipprofile_data['name']))
	  {
	    $this->session->set_flashdata('astpp_notification', 'Sip Profile name must not have any space!');
 	    redirect(base_url() . 'freeswitch/fssipprofile_add/');
 	    exit;
	  }
	  if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $sipprofile_data['sip_ip']))
	  {
	    $this->session->set_flashdata('astpp_notification', 'Sip IP must be proper!');
 	    redirect(base_url() . 'freeswitch/fssipprofile_add/');
 	    exit;
	  }
	  

	  
	  $sipprofile_data['id']='';
	  $check_authentication = $this->freeswitch_model->profile_authentication($sipprofile_data);
	  if ($check_authentication->num_rows == 0) {
	      
	      $sipprofile_data['created_date']=gmdate('Y-m-d H:i:s'); 
	      $insert = $this->db->insert("sip_profiles", $sipprofile_data);

	    }
	    else {
                    $this->session->set_flashdata('astpp_notification', 'Duplicate SIP IP OR Port found it must be unique!');
		    redirect(base_url() . 'freeswitch/fssipprofile_add/');
	    }
	    redirect(base_url() . 'freeswitch/fssipprofile/');
        }
	
        if($add == 'edit')
        {
            
// 	    echo "<pre>";
// 	    print_r($sipprofile_data);
// 	    exit;
          
	  $check_authentication = $this->freeswitch_model->profile_authentication($sipprofile_data);
	    unset($sipprofile_data['action']);
	    unset($sipprofile_data['sipstatus']);
	    $insert_arr = $sipprofile_data;
	        if ($check_authentication->num_rows == 0) {
                    $update = $this->db->update("sip_profiles", $insert_arr, array('id' => $sipprofile_data['id']));
                    $this->session->set_flashdata('astpp_errormsg', $sipprofile_data['name']." SIP Profile Updated Successfully!");
                    redirect(base_url() . 'freeswitch/fssipprofile/');   
                    exit;
                } else {
                    $this->session->set_flashdata('astpp_notification', 'Duplicate SIP IP OR Port found it must be unique!');
		    redirect(base_url() . 'freeswitch/fssipprofile/');
                }
	  redirect(base_url() . 'freeswitch/fssipprofile/');   
        }
        $this->load->view('view_fssipprofile_add', $data);
    }


    function fssipprofile_edit($edit_id = '',$type='',$name_prams='') {
    
        
        $data['page_title'] = 'Edit Sip Profile';
          $sipprofile_data = $this->input->post();
        
        if(!$edit_id)
        {
	  $edit_id=$sipprofile_data['id'];
        }
        
        $where = array('id' => $edit_id);
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        $query = $query->result_array();
//         echo $this->db->last_query();exit;
        $gateway_result = array();
        
        foreach ($query as $key => $query_value) {
            foreach ($query_value as $gateway_key => $gatewau_val) {
                if ($gateway_key != "profile_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
            }
        }
//         $data['form'] = $this->form->build_form($this->freeswitch_form->get_sipprofile_form_fields(), $gateway_result);
        
        $data['grid_fields'] = $this->freeswitch_form->build_fssipprofile_params_list_for_admin();
        $data['edited_id'] = $edit_id;
        
        $data['sip_name']=$query[0]['name'];
	$data['status']=$query[0]['status'];
	$data['sip_ip']= $query[0]['sip_ip'];
	$data['sip_port']=$query[0]['sip_port'];
	$data['id']=$query[0]['id'];
        $data['button_name']="Add Setting";
        
      
//         echo "<pre>";print_r($sipprofile_data);exit;
        if($type == 'edit' || isset($sipprofile_data['type']) && $sipprofile_data['type'] == 'save')
        {
	    if($type == 'edit')
	    {
// 		echo "<pre>";print_r($query);
// 		print_r($gateway_result);exit;
		$data['params_name']=$name_prams;
		$data['params_status']=0;
// 		echo $gateway_result[$name_prams];
		if($gateway_result[$name_prams] == "true" || $gateway_result[$name_prams] == "false")
		{
		  $data['params_status']=1;
		}
		$data['params_value']=$gateway_result[$name_prams];
		$data['button_name']="Update Setting";
	    }
	    if(isset($sipprofile_data['type']) && $sipprofile_data['type'] == 'save'){
		$sipprofile_data = $this->input->post();
		$tmp[$sipprofile_data['params_name']]=$sipprofile_data['params_value'];
	  // 	print_r($tmp);
		  $final_data= json_encode($tmp);
	  // 	echo $final_data;
	  // 	exit;
		  $insert_arr["profile_data"] = json_encode($tmp);
		  $update = $this->db->update("sip_profiles", $insert_arr, array('id' => $edit_id));
		   if($sipprofile_data['type_settings']=="add_setting"){
		    $this->session->set_flashdata('astpp_errormsg',$data['sip_name']. " SIP Setting Added Successfully!");
                  }else{
		    $this->session->set_flashdata('astpp_errormsg',$data['sip_name']. " SIP Setting Updated Successfully!");
		    
                  }
                  redirect(base_url() . 'freeswitch/fssipprofile_edit/'.$sipprofile_data['id']);
		  exit;

	    }
        }
//         echo "<pre>";
//         print_r($data);
        $this->load->view('view_fssipprofile_edit', $data);
    }

   function fssipprofile_save($id) {
	$sipprofile_data = $this->input->post();
	$insert_arr = array();
        $sipprofile_arr = array();
        
// 	echo "<pre>";
// 	print_r($sipprofile_data);
	
	
	$where = array('id' => $id);
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        $query = $query->result_array();
        $gateway_result = array();
        foreach ($query as $key => $query_value) {
            foreach ($query_value as $gateway_key => $gatewau_val) {
                if ($gateway_key != "profile_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
            }
        }
//         print_r($tmp);
//         print_r($gateway_data);
        $tmp[$sipprofile_data['params_name']]=$sipprofile_data['params_value'];
// 	print_r($tmp);
	$final_data= json_encode($tmp);
// 	echo $final_data;
// 	exit;
	$insert_arr["profile_data"] = json_encode($tmp);
	$update = $this->db->update("sip_profiles", $insert_arr, array('id' => $id));

	
	$this->load->view('view_fssipprofile_edit', $data);
// 	exit;
    }
    
    function fssipprofile_delete_params($id,$name) {
	$where = array('id' => $id);
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        $query = $query->result_array();
        $gateway_result = array();
        foreach ($query as $key => $query_value) {
            foreach ($query_value as $gateway_key => $gatewau_val) {
                if ($gateway_key != "profile_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
            }
        }
	if(isset($tmp[$name])){
	  unset($tmp[$name]);
	}
// 	print_r($tmp);
// 	exit;
	$insert_arr["profile_data"] = json_encode($tmp);
	$update = $this->db->update("sip_profiles", $insert_arr, array('id' => $id));
	
	
	
// 	$profile_name = $this->common->get_field_name('name', 'sip_profiles', $id);
//         $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $id);
	
        $this->session->set_flashdata('astpp_notification', $name.' SIP Setting Removed Successfully!');
        redirect(base_url() . 'freeswitch/fssipprofile_edit/'.$id);
    }
    
    function fssipprofile_delete($profile_id) {
	$profile_name = $this->common->get_field_name('name', 'sip_profiles', $profile_id);
        $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $profile_id);
	$delete = $this->db_model->delete("sip_profiles", array("id" => $profile_id));
        $this->session->set_flashdata('astpp_notification', 'Sip Profile Removed Successfully!');
        redirect(base_url() . 'freeswitch/fssipprofile/');
    }

    function fsserver_list() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Freeswitch Servers';
        $data['search_flag'] = true;
        $data['cur_menu_no'] = 1;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_fsserver_list();
        $data["grid_buttons"] = $this->freeswitch_form->build_fsserver_grid_buttons();

        $data['form_search'] = $this->form->build_serach_form($this->freeswitch_form->get_search_fsserver_form());
        $this->load->view('view_fsserver_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function fsserver_list_json() {
        $json_data = array();

        $count_all = $this->freeswitch_model->get_fsserver_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->freeswitch_model->get_fsserver_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->freeswitch_form->build_fsserver_list());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function fsserver_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Freeswich Server';
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_form_fsserver_fields(), '');

        $this->load->view('view_fsserver_add_edit', $data);
    }

    function fsserver_edit($edit_id = '') {
        $data['page_title'] = 'Edit Freeswich Server';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "freeswich_servers", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_form_fsserver_fields(), $edit_data);
        $this->load->view('view_fsserver_add_edit', $data);
    }

    function fsserver_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->freeswitch_form->get_form_fsserver_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Freeswitch Server';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_fsserver($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> " Freeswitch Server Updated Successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Freeswich Server';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_fssever($add_array);
                echo json_encode(array("SUCCESS"=> "Freeswitch Server Added Successfully!"));
                exit;
            }
        }
        $this->load->view('view_callshop_details', $data);
    }

    function fsserver_delete($id) {
        $this->freeswitch_model->fsserver_delete($id);
        $this->session->set_flashdata('astpp_notification', 'Freeswitch Server Removed Successfully!');
        redirect(base_url() . 'freeswitch/fsserver_list/');
        exit;
    }

    function fsserver_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fsserver_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'freeswitch/fsserver_list/');
        }
    }

    function fsserver_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

}

?>
 
