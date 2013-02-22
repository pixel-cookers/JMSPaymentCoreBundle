<?php

namespace Up2green\PropelPaymentCoreBundle\Model;

use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use Up2green\PropelPaymentCoreBundle\Model\om\BaseFinancialTransaction;

/**
 * Financial transaction entity
 */
class FinancialTransaction extends BaseFinancialTransaction implements FinancialTransactionInterface
{
    public function getExtendedData(PropelPDO $con = null, $doQuery = true)
    {

        if (null !== ($data = parent::getExtendedData($con, $doQuery))) {
            return $data;
        }

        if (null !== $this->getPayment()) {
            return $this->getPayment()->getPaymentInstruction()->getExtendedData();
        } elseif (null !== $this->getCredit()) {
            return $this->getCredit()->getPaymentInstruction()->getExtendedData();
        }

        return null;
    }

    public function setTransactionType($transactionType)
    {
        switch ($transactionType) {
            case FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE :
                parent::setTransactionType('approve');
                break;
            case FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE_AND_DEPOSIT :
                parent::setTransactionType('approve-and-deposit');
                break;
            case FinancialTransactionInterface::TRANSACTION_TYPE_CREDIT :
                parent::setTransactionType('credit');
                break;
            case FinancialTransactionInterface::TRANSACTION_TYPE_DEPOSIT :
                parent::setTransactionType('deposit');
                break;
            case FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_APPROVAL :
                parent::setTransactionType('reverse-approval');
                break;
            case FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_CREDIT :
                parent::setTransactionType('reverse-credit');
                break;
            case FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_DEPOSIT :
                parent::setTransactionType('reverse-deposit');
                break;
            default:
                parent::setTransactionType($transactionType);
                break;
        }
    }

    public function getTransactionType()
    {
        $constantName = strtoupper(parent::getTransactionType());
        $constantName = str_replace('-', '_', $constantName);

        return constant('JMS\Payment\CoreBundle\Model\FinancialTransactionInterface::TRANSACTION_TYPE_' . $constantName);
    }

    public function setState($state)
    {
        switch ($state) {
            case FinancialTransactionInterface::STATE_CANCELED :
                parent::setState('canceled');
                break;
            case FinancialTransactionInterface::STATE_FAILED :
                parent::setState('failed');
                break;
            case FinancialTransactionInterface::STATE_NEW :
                parent::setState('new');
                break;
            case FinancialTransactionInterface::STATE_PENDING :
                parent::setState('pending');
                break;
            case FinancialTransactionInterface::STATE_SUCCESS :
                parent::setState('success');
                break;
            default:
                parent::setState($state);
                break;
        }
    }

    public function getState()
    {
        return constant('JMS\Payment\CoreBundle\Model\FinancialTransactionInterface::STATE_'.strtoupper(parent::getState()));
    }
}
