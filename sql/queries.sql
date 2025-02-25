SELECT
    setting.step,
    office.office_code,
    office.office_name,
    person.name AS person_in_charge
FROM document_transaction_setting setting
LEFT JOIN offices office ON setting.office = office.id
LEFT JOIN users person ON office.person_in_charge = person.id
WHERE doc_type = 2
ORDER BY setting.step;

/* for logs */
SELECT
    doc_log.status,
    office.office_code,
    office.office_name,
    person.name AS person_in_charge
FROM document_transaction_logs doc_log
LEFT JOIN offices office ON doc_log.office = office.id
LEFT JOIN users person ON doc_log.updated_by = person.id 
WHERE doc_log.doc_number = ?
    AND doc_log.step = ?
