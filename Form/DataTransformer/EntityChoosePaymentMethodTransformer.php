<?php

namespace JMS\Payment\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use JMS\Payment\CoreBundle\Entity\ExtendedData;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

class EntityChoosePaymentMethodTransformer implements DataTransformerInterface
{
    public function transform($data, array $options)
    {
        if (null === $data) {
            return null;
        }

        if ($data instanceof PaymentInstruction) {
            $method = $data->getPaymentSystemName();
            $methodData = array_map(function($v) { return $v[0]; }, $data->getExtendedData()->all());
            if (isset($options['predefined_data'][$method])) {
                $methodData = array_diff_key($methodData, $options['predefined_data'][$method]);
            }

            return array(
                'method'        => $method,
                'data_'.$method => $methodData,
            );
        }

        throw new \RuntimeException(sprintf('Unsupported data of type "%s".', ('object' === $type = gettype($data)) ? get_class($data) : $type));
    }

    public function reverseTransform($data, array $options)
    {
        $method = isset($data['method']) ? $data['method'] : null;
        $data = isset($data['data_'.$method]) ? $data['data_'.$method] : array();

        $extendedData = new ExtendedData();
        foreach ($data as $k => $v) {
            $extendedData->set($k, $v);
        }

        if (isset($options['predefined_data'][$method])) {
            if (!is_array($options['predefined_data'][$method])) {
                throw new \RuntimeException(sprintf('"predefined_data" is expected to be an array for each method, but got "%s" for method "%s".', json_encode($options['extra_data'][$method]), $method));
            }

            foreach ($options['predefined_data'][$method] as $k => $v) {
                $extendedData->set($k, $v);
            }
        }

        return new PaymentInstruction($options['amount'], $options['currency'], $method, $extendedData);
    }
}