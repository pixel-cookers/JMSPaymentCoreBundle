<?php

namespace Up2green\PropelPaymentCoreBundle\Model;

use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use Up2green\PropelPaymentCoreBundle\Model\om\BasePayment;

class Payment extends BasePayment implements PaymentInterface
{
    /**
     * @return FinancialTransactionInterface
     */
    public function getApproveTransaction()
    {
        foreach ($this->getFinancialTransactions() as $transaction) {
            $type = $transaction->getTransactionType();

            if (FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE === $type
                || FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE_AND_DEPOSIT === $type) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getDepositTransactions()
    {
        $criteria = new \Criteria();
        $criteria->add('transactionType', FinancialTransactionInterface::TRANSACTION_TYPE_DEPOSIT);

        return $this->getFinancialTransactions($criteria);
    }

    /**
     * @return FinancialTransactionInterface
     */
    public function getPendingTransaction()
    {
        foreach ($this->getFinancialTransactions() as $transaction) {
            if (FinancialTransactionInterface::STATE_PENDING === $transaction->getState()) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function hasPendingTransaction()
    {
        return null !== $this->getPendingTransaction();
    }

    /**
     * @return array
     */
    public function getReverseApprovalTransactions()
    {
        $criteria = new \Criteria();
        $criteria->add('transactionType', FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_APPROVAL);

        return $this->getFinancialTransactions($criteria);
    }

    /**
     * @return array
     */
    public function getReverseDepositTransactions()
    {
        $criteria = new \Criteria();
        $criteria->add('transactionType', FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_DEPOSIT);

        return $this->getFinancialTransactions($criteria);
    }

    /**
     * @return boolean
     */
    public function isAttentionRequired()
    {
        return $this->getAttentionRequired();
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        return $this->getExpired();
    }

    public function setState($state)
    {
        switch ($state) {
            case PaymentInterface::STATE_APPROVED :
                parent::setState('approved');
                break;
            case PaymentInterface::STATE_APPROVING :
                parent::setState('approving');
                break;
            case PaymentInterface::STATE_CANCELED :
                parent::setState('canceled');
                break;
            case PaymentInterface::STATE_DEPOSITED :
                parent::setState('deposited');
                break;
            case PaymentInterface::STATE_DEPOSITING :
                parent::setState('depositing');
                break;
            case PaymentInterface::STATE_EXPIRED :
                parent::setState('expired');
                break;
            case PaymentInterface::STATE_FAILED :
                parent::setState('failed');
                break;
            case PaymentInterface::STATE_NEW :
                parent::setState('new');
                break;
            default:
                parent::setState($state);
                break;
        }
    }

    public function addTransaction($transaction)
    {
        $this->addFinancialTransaction($transaction);
    }

    public function getState()
    {
        return constant('JMS\Payment\CoreBundle\Model\PaymentInterface::STATE_'.strtoupper(parent::getState()));
    }
}
