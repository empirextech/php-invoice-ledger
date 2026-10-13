<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Plan;

final class PlanLimiter
{
    public function canAddCompany(Plan $plan, int $ownedCompanies): Decision
    {
        if ($plan->maxCompanies === null) {
            return new Decision(true, null, $ownedCompanies, null);
        }
        if ($ownedCompanies >= $plan->maxCompanies) {
            return new Decision(false, 'Company limit reached for plan ' . $plan->name . '.', $ownedCompanies, $plan->maxCompanies);
        }

        return new Decision(true, null, $ownedCompanies, $plan->maxCompanies);
    }

    public function canIssueDocument(Plan $plan, int $issuedThisMonth): Decision
    {
        if ($plan->maxDocumentsPerMonth === null) {
            return new Decision(true, null, $issuedThisMonth, null);
        }
        if ($issuedThisMonth >= $plan->maxDocumentsPerMonth) {
            return new Decision(false, 'Monthly document limit reached for plan ' . $plan->name . '.', $issuedThisMonth, $plan->maxDocumentsPerMonth);
        }

        return new Decision(true, null, $issuedThisMonth, $plan->maxDocumentsPerMonth);
    }
}
