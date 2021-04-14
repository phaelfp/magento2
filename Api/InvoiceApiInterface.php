<?php

namespace MundiPagg\MundiPagg\Api;

interface InvoiceApiInterface
{
    /**
     * Get invoice
     *
     * @param string $id
     * @return mixed
     */
    public function getByInvoiceId($id);

    /**
     * Cancel invoice
     *
     * @param string $id
     * @return mixed
     */
    public function cancelByInvoiceId($id);

}