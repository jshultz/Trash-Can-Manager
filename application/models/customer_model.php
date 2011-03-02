<?php

        class Customer_model extends CI_Model {

            function getCustomers() {
                $userid = $this->tank_auth->get_user_id();
                $this->db->select('n.firstname, n.lastname, a.city, p.phonenumber, n.custid, p.custid, a.custid, t.quantity');
                $this->db->from('custname as n');
                $this->db->where('n.userid',$userid);
                $this->db->where('n.is_archived',0);
                $this->db->join('custaddress as a', 'a.custid = n.custid', 'left');
                $this->db->join('custphone as p', 'p.custid = n.custid', 'left');
                $this->db->join('custtrashcans as t', 't.custid = n.custid', 'left');
                $this->db->group_by('n.custid');
                return $this->db->get();
            }

            function getAddress($id) {
                $userid = $this->tank_auth->get_user_id();
                $this->db->select('a.street1, a.street2, a.city, a.state, a.zip');
                $this->db->from('custaddress as a');
                $this->db->where('a.custid',$id);
                $q = $this->db->get();
                return $q->row();
            }

            function getCustEdit($id) {
                $userid = $this->tank_auth->get_user_id();
                $this->db->select('n.firstname, n.lastname, a.city, p.phonenumber, n.custid, p.custid, a.custid, a.street1, a.street2, a.city, a.state, a.zip, p.phonenumber');
                $this->db->from('custname as n');
                $this->db->where('n.userid',$userid);
                $this->db->where('n.custid',$id);
                $this->db->join('custaddress as a', 'a.custid = n.custid', 'left');
                $this->db->join('custphone as p', 'p.custid = n.custid', 'left');
                $this->db->group_by('n.custid');
                return $this->db->get();
            }

            function archiveCust($user_id, $custid, $goback) {
                $this->db->select('c.custid, c.is_archived, c.userid');
                $this->db->from('custname AS c');
                $this->db->where('custid', $custid);
                $this->db->where('userid', $user_id);
                    $data = array(
                        'is_archived' => '1'
                    );
                $this->db->update('custname', $data);
                redirect($goback);
            }

            function updateCust($custid, $lastname, $firstname, $street1, $street2, $city, $state, $zip, $phonenumber, $userid, $goback) {

                $this->db->select('c.custid');
                $this->db->from('custname AS c');
                $this->db->where('c.custid', $custid);
                $this->db->where('c.userid', $userid);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    /* Update Customer */

                    $datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
                    $time = date('Y-m-d h:i:s');

                   $data1 = array(
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'userid' => $userid,
                    'custid' => $custid,
                    'modified' => $time,
                );

                    $this->db->where('custid', $custid);
                   $this->db->update('custname', $data1);

                   $data2  = array(
                    'street1' => $street1,
                    'street2' => $street2,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'custid' => $custid,
                );

                    $this->db->where('custid', $custid);
                   $this->db->update('custaddress', $data2);

                $data3 = array(
                    'phonenumber' => $phonenumber,
                    'custid' => $custid,
                );

                $this->db->where('custid', $custid);
                $this->db->update('custphone', $data3);

                redirect($goback);

                }

                /* Create Customer */

                else {

                    $datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
                    $time = date('Y-m-d h:i:s');

                    $data1 = array(
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'userid' => $userid,
                    'created' => $time
                );

                $this->db->insert('custname', $data1);

                $custid = $this->db->insert_id();

                $data2  = array(
                    'street1' => $street1,
                    'street2' => $street2,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'custid' => $custid,
                );

                $this->db->insert('custaddress', $data2);

                $data3 = array(
                    'phonenumber' => $phonenumber,
                    'custid' => $custid,
                );

                $this->db->insert('custphone', $data3);

                redirect($goback);
                }






            }


        }
/**
 * Created by JetBrains PhpStorm.
 * User: Jason Shultz
 * Date: 1/11/11
 * Time: 2:30 PM
 */
 