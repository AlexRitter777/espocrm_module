<?php

namespace Espo\Modules\Autocrm\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\ORM\EntityManager;


class FindContactsController
{

    public function __construct(private EntityManager $entityManager){

    }

    /**
     *  GET api/v1/Account/action/test
     */
    public function getActionFindContacts(Request $request, Response $response)
    {
        $result = [];

        $leadId = $request->getQueryParam('id'); //lead id from GET

         //Get lead from leads table
        if (!$lead = $this->entityManager->getEntityById('Lead', $leadId)) {
            return $this->responseToClient(404, 'Lead not found!', $response);
        }

        //Get lead email
        if(!$email = $lead->get('emailAddress')) {
            return $this->responseToClient(404, 'Lead has no email!',$response);
        }

        //Get e-mail (table email_address)
        $emailAddress = $this->entityManager->getRepository('EmailAddress')->where([
            'lower' => strtolower($email)
        ])->findOne();

        if(!$emailAddress) {
            return $this->responseToClient(404, 'Email address not found!', $response);
        }

        //Get e-mail Id
        $emailId = $emailAddress->getId();
        //Get contacts dd with same e-mail id
        $entityEmailAddresses = $this->entityManager->getRepository('EntityEmailAddress')->where([
            'emailAddressId' => $emailId,
            'entityType' => 'Contact'
        ])->find();

        if (empty($entityEmailAddresses)) {
            return $this->responseToClient(404, 'No contacts found for this email address!', $response);
        }

        //iterate contacts and get name
        foreach ($entityEmailAddresses as $entityEmailAddress) {
            $entityId = $entityEmailAddress->get('entityId');
            $contact = $this->entityManager->getEntity('Contact', $entityId);
            $result[] = $contact->get('name');
        }

        //return result
        return $this->responseToClient(200, json_encode($result), $response);


    }

    private function responseToClient(int $code, string $message, Response $response): Response
    {
        $response->setStatus($code);
        $response->setHeader('Content-Type', 'application/json');
        $response-> writeBody($message);
        return $response;
    }

}