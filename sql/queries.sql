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


/* get submitter notifs */
SELECT
    log.id,
    doc.doc_number,
    log.status,
    log.tstamp
FROM document_transaction_logs log
LEFT JOIN submitted_documents doc
    ON log.doc_number = doc.doc_number
WHERE doc.user_id = 18
ORDER BY log.tstamp DESC;

/* get settings for office if there is */
SELECT
    GROUP_CONCAT(DISTINCT setting.doc_type)
FROM document_transaction_setting setting
LEFT JOIN document_types type
    ON setting.doc_type = type.id
WHERE setting.office = 13;

/* get documents designated to the office */
SELECT
    doc.doc_number,
    doc.doc_type,
    MAX(log.step) + 1 AS current_step,
    user.name as submitter,
    doc_type.doc_type_name,
    doc.purpose,
    doc.status,
    doc.tstamp
FROM submitted_documents doc
LEFT JOIN document_transaction_logs log ON doc.doc_number = log.doc_number
LEFT JOIN document_types doc_type ON doc.doc_type = doc_type.id 
LEFT JOIN users user ON doc.user_id = user.id
WHERE doc.status NOT IN('Rejected','For Release') AND doc.doc_type IN(2,3)
GROUP BY doc.doc_number

/* check if document is designated to the office */
SELECT id FROM document_transaction_setting WHERE doc_type = 2 AND step = 1 AND office = 23;

/* get document handled by offices */
SELECT
    doc.doc_number,
    type.doc_type_name,
    user.name AS submitter,
    doc.purpose,
    log.status AS action_taken,
    log.tstamp AS date_of_action
FROM document_transaction_logs log
LEFT JOIN submitted_documents doc ON log.doc_number = doc.doc_number
LEFT JOIN document_types type ON doc.doc_type = type.id
LEFT JOIN users user ON doc.user_id = user.id
WHERE log.office = 12;