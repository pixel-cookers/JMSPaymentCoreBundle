<?php

namespace Up2green\PropelPaymentCoreBundle\Model;

use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Model\CreditInterface;
use Up2green\PropelPaymentCoreBundle\Model\om\BaseCredit;

/**
 * Credit entity
 */
class Credit extends BaseCredit implements CreditInterface
{
    /**
     * @return FinancialTransactionInterface
     */
    public function getCreditTransaction()
    {
        foreach ($this->getFinancialTransactions() as $transaction) {
            if (FinancialTransactionInterface::TRANSACTION_TYPE_CREDIT === $transaction->getTransactionType()) {
                return $transaction;
            }
        }

        return null;
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
     * @return array
     */
    public function getReverseCreditTransactions()
    {
        $criteria = new \Criteria();
        $criteria->add('transactionType', FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_CREDIT);

        return $this->getFinancialTransactions($criteria);
    }

    /**
     * @return boolean
     */
    public function hasPendingTransaction()
    {
        return null !== $this->getPendingTransaction();
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
    public function isIndependent()
    {
        return null === $this->payment;
    }

    public function setState($state)
    {
        switch ($state) {
            case CreditInterface::STATE_CANCELED :
                parent::setState('canceled');
                break;
            case CreditInterface::STATE_CREDITED :
                parent::setState('credited');
                break;
            case CreditInterface::STATE_CREDITING :
                parent::setState('crediting');
                break;
            case CreditInterface::STATE_FAILED :
                parent::setState('failed');
                break;
            case CreditInterface::STATE_NEW :
                parent::setState('new');
                break;
            default:
                parent::setState($state);
                break;
        }
    }

    public function getState()
    {
        return constant('JMS\Payment\CoreBundle\Model\CreditInterface::STATE_'.strtoupper(parent::getState()));
    }
}
