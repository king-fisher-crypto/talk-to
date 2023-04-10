<?php
class CronShell extends AppShell {
    public function getInclusions()
    {
        App::uses('CakeRequest', 'Network');
        App::uses('CakeResponse', 'Network');
        App::uses('Controller', 'Controller');
        App::uses('CronController', 'Controller');
        return new CronController(new CakeRequest(), new CakeResponse());

    }
    public function horoscope_rotate()
    {
        $ctrl = $this->getInclusions();
        $ctrl->horoscope_rotate();
    }
    public function clearcreditlasthistory()
    {
        $ctrl = $this->getInclusions();
        $ctrl->clearcreditlasthistory();
    }
    public function savemessagehistory()
    {
        $ctrl = $this->getInclusions();
        $ctrl->savemessagehistory();
    }
    public function clearappointments()
    {
        $ctrl = $this->getInclusions();
        $ctrl->clearappointments();
    }
    public function sendappointments()
    {
        $ctrl = $this->getInclusions();
        $ctrl->initEmailParameters();
        Configure::write('Email.template.vars.PARAM_URLSITE', Configure::read('Site.baseUrlFull'));
        Configure::write('Email.template.with_footer', false);
        $tmp = Configure::read('Email.template.vars');

        $ctrl->sendappointments();
    }
    public function clearphonealerts()
    {
        $ctrl = $this->getInclusions();
        $ctrl->clearphonealerts();
    }
    public function clearalerts()
    {
        $ctrl = $this->getInclusions();
        $ctrl->clearalerts();
    }
	public function sendalerts()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendalerts();
    }

    public function closechat()
    {
        $ctrl = $this->getInclusions();
        $ctrl->closechat();
    }

	public function autoConnectAgent()
    {
        $ctrl = $this->getInclusions();
        $ctrl->autoConnectAgent();
    }
	public function autoSortExpert()
    {
        $ctrl = $this->getInclusions();
        $ctrl->autoSortExpert();
    }

	public function orderExpert()
    {
        $ctrl = $this->getInclusions();
        $ctrl->orderExpert();
    }

	public function removeRecords()
    {
        $ctrl = $this->getInclusions();
        $ctrl->removeRecords();
    }
	public function alertSMSComTchat()
    {
        $ctrl = $this->getInclusions();
        $ctrl->alertSMSComTchat();
    }
	public function alertSMSComMail()
    {
        $ctrl = $this->getInclusions();
        $ctrl->alertSMSComMail();
    }
	public function deleteComDuplicate()
    {
        $ctrl = $this->getInclusions();
        $ctrl->deleteComDuplicate();
    }
	public function crmSend()
    {
        $ctrl = $this->getInclusions();
        $ctrl->crmSend();
    }
	public function checkCallNoResponse()
    {
        $ctrl = $this->getInclusions();
        $ctrl->checkCallNoResponse();
    }
	public function clearBonus()
    {
        $ctrl = $this->getInclusions();
        $ctrl->clearBonus();
    }
	public function sendRelance()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendRelance();
    }
	public function checkCostAgent()
    {
        $ctrl = $this->getInclusions();
        $ctrl->checkCostAgent();
    }
	public function checkNextCostAgent()
    {
        $ctrl = $this->getInclusions();
        $ctrl->checkNextCostAgent();
    }
	public function sendQueueRelanceAgent()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendQueueRelanceAgent();
    }
	public function alertCustomerCredit()
    {
        $ctrl = $this->getInclusions();
        $ctrl->alertCustomerCredit();
    }
	public function relanceReviews()
    {
        $ctrl = $this->getInclusions();
        $ctrl->relanceReviews();
    }
	public function generatePhotoFBAgent()
    {
        $ctrl = $this->getInclusions();
        $ctrl->generatePhotoFBAgent();
    }
	public function getExports()
    {
        $ctrl = $this->getInclusions();
        $ctrl->getExports();
    }
	public function unlockSponsorship()
    {
        $ctrl = $this->getInclusions();
        $ctrl->unlockSponsorship();
    }
	public function sendSponsorship()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendSponsorship();
    }
	public function notEnoughtExpert()
    {
        $ctrl = $this->getInclusions();
        $ctrl->notEnoughtExpert();
    }
	public function sendManualSponsorship()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendManualSponsorship();
    }
	public function sendHoroscope()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendHoroscope();
    }
	public function sendHoroscopeDominical()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendHoroscopeDominical();
    }
	public function simultaneousCommunication()
    {
        $ctrl = $this->getInclusions();
        $ctrl->simultaneousCommunication();
    }
	public function sendGift()
    {
        $ctrl = $this->getInclusions();
        $ctrl->sendGift();
    }
	public function userInvoice()
    {
        $ctrl = $this->getInclusions();
        $ctrl->userInvoice();
    }
	public function checkSEPA()
    {
        $ctrl = $this->getInclusions();
        $ctrl->checkSEPA();
    }
	public function generateInvoiceAccount()
    {
        $ctrl = $this->getInclusions();
        $ctrl->generateInvoiceAccount();
    }
	public function generateInvoiceAgent()
    {
        $ctrl = $this->getInclusions();
        $ctrl->generateInvoiceAgent();
    }

	public function getStripeBalance()
    {
        $ctrl = $this->getInclusions();
        $ctrl->getStripeBalance();
    }

	public function addrecords()
    {
        $ctrl = $this->getInclusions();
        $ctrl->addrecords();
    }

	public function checkVatStatus()
    {
        $ctrl = $this->getInclusions();
        $ctrl->checkVatStatus();
    }

	public function supportReadMail()
    {
        $ctrl = $this->getInclusions();
        $ctrl->supportReadMail();
    }

	public function calcExportCom()
    {
        $ctrl = $this->getInclusions();
        $ctrl->calcExportCom();
    }
	public function calcExportComUpdate()
    {
        $ctrl = $this->getInclusions();
        $ctrl->calcExportComUpdate();
    }
  public function updateCurrencies()
    {
        $ctrl = $this->getInclusions();
        $ctrl->updateCurrencies();
    }
  public function alertWrongCustomer()
    {
        $ctrl = $this->getInclusions();
        $ctrl->alertWrongCustomer();
    }
  public function updateClientList()
    {
        $ctrl = $this->getInclusions();
        $ctrl->updateClientList();
    }
  public function SaleReconciliation()
    {
        $ctrl = $this->getInclusions();
        $ctrl->SaleReconciliation();
    }
  public function updateClientTarotList()
    {
        $ctrl = $this->getInclusions();
        $ctrl->updateClientTarotList();
    }
  public function updateOrderConversion()
    {
        $ctrl = $this->getInclusions();
        $ctrl->updateOrderConversion();
    }
}
