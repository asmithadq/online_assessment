<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html 
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = SITECANDIDATE.'/Login/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*---------------------------------Admin Routes-------------------------------------------------*/

$route['admin-login']                       = SITEADMIN.'/Login/index';
$route['cms-login']                         = SITEADMIN.'/Login/index';
$route['admin-logout']                      = SITEADMIN.'/Login/logout';
$route['permission-denied']                 = SITEADMIN.'/Permission/index';
$route['admin-dashboard']                   = SITEADMIN.'/Dashboard/index';
$route['list-upcoming-assessments']         = SITEADMIN.'/Dashboard/getUpcomingAssessments';

/* Masters :: List/Add/Edit/Delete */
$route['list-schemes']                      = SITEADMIN.'/Schemes/list';
$route['save-scheme']                       = SITEADMIN.'/Schemes/save';
$route['delete-scheme/(:any)']              = SITEADMIN.'/Schemes/delete/$1';
$route['check-duplicate-scheme']            = SITEADMIN.'/Schemes/CheckDuplicateScheme';

$route['list-subschemes']                   = SITEADMIN.'/Subschemes/list';
$route['save-subscheme']                    = SITEADMIN.'/Subschemes/save';
$route['delete-subscheme/(:any)']           = SITEADMIN.'/Subschemes/delete/$1';
$route['check-duplicate-sub-scheme']        = SITEADMIN.'/Subschemes/CheckDuplicateSubScheme';

$route['list-states']                       = SITEADMIN.'/States/list';
$route['save-state']                        = SITEADMIN.'/States/save';
$route['delete-state/(:any)']               = SITEADMIN.'/States/delete/$1';

$route['list-districts']                    = SITEADMIN.'/Districts/list';
$route['save-district']                     = SITEADMIN.'/Districts/save';
$route['delete-district/(:any)']            = SITEADMIN.'/Districts/delete/$1';

$route['list-languages']                    = SITEADMIN.'/Languages/list';
$route['save-language']                     = SITEADMIN.'/Languages/save';
$route['delete-language/(:any)']            = SITEADMIN.'/Languages/delete/$1';
$route['check-duplicate-language-name']     = SITEADMIN.'/Languages/CheckDuplicateLanguageName';

$route['list-banks']                        = SITEADMIN.'/Banks/list';
$route['save-bank']                         = SITEADMIN.'/Banks/save';
$route['delete-bank/(:any)']                = SITEADMIN.'/Banks/delete/$1';

$route['list-nos']                          = SITEADMIN.'/Nos/list';
$route['save-nos']                          = SITEADMIN.'/Nos/save';
$route['delete-nos/(:any)']                 = SITEADMIN.'/Nos/delete/$1';
$route['check-duplicate-nos-code']          = SITEADMIN.'/Nos/CheckDuplicateNOSCode';

$route['list-assessment-documents-master']  = SITEADMIN.'/Documents/list';
$route['save-assessment-document']          = SITEADMIN.'/Documents/save';
$route['delete-assessment-document/(:any)'] = SITEADMIN.'/Documents/delete/$1';
$route['check-duplicate-document-title']    = SITEADMIN.'/Documents/CheckDuplicateDocumentTitle';

$route['list-email-templates']              = SITEADMIN.'/Settings/list';
$route['save-email']                        = SITEADMIN.'/Settings/save';
$route['delete-email/(:any)']               = SITEADMIN.'/Settings/delete/$1';
$route['get-mapped-email-content']          = SITEADMIN.'/Settings/viewMappedEmailContent';
$route['edit-email-template']               = SITEADMIN.'/Settings/editEmailTemplate';

$route['list-nsfq-levels']                  = SITEADMIN.'/Nsfq/list';
$route['save-nsfqlevel']                    = SITEADMIN.'/Nsfq/save';
$route['delete-nsfqlevel/(:any)']           = SITEADMIN.'/Nsfq/delete/$1';

$route['list-versions']                     = SITEADMIN.'/Versions/list';
$route['save-version']                      = SITEADMIN.'/Versions/save';
$route['delete-version/(:any)']             = SITEADMIN.'/Versions/delete/$1';

/* Training Partners Menu :: List/Add/Edit/Delete  */
$route['list-training-partners']            = SITEADMIN.'/Partners/list';
$route['list-training-partners-ajax']       = SITEADMIN.'/Partners/getLists';
$route['create-training-partners']          = SITEADMIN.'/Partners/viewAddEditForm';
$route['edit-training-partner/(:any)']      = SITEADMIN.'/Partners/viewAddEditForm/$1';
$route['check-duplicate-tp-code']           = SITEADMIN.'/Partners/CheckDuplicateTpCode';
$route['save-training-partner']             = SITEADMIN.'/Partners/save';
$route['delete-training-partner/(:any)']    = SITEADMIN.'/Partners/delete/$1';

/* Training Centers Menu :: List/Add/Edit/Delete  */
$route['list-training-centers']             = SITEADMIN.'/Centers/list';
$route['list-training-centers-ajax']        = SITEADMIN.'/Centers/getLists';
$route['create-training-centers']           = SITEADMIN.'/Centers/viewAddEditForm';
$route['edit-training-center/(:any)']       = SITEADMIN.'/Centers/viewAddEditForm/$1';
$route['check-duplicate-tc-code']           = SITEADMIN.'/Centers/CheckDuplicateTcCode';
$route['save-training-center']              = SITEADMIN.'/Centers/save';
$route['delete-training-center/(:any)']     = SITEADMIN.'/Centers/delete/$1';

/* Trade Menu :: List/Add/Edit/Delete  */
$route['list-trades']                       = SITEADMIN.'/Trades/list';
$route['list-trades-ajax']                  = SITEADMIN.'/Trades/getLists';
$route['create-trade-nos']                  = SITEADMIN.'/Trades/viewAddEditForm';
$route['edit-trade-nos/(:any)']             = SITEADMIN.'/Trades/viewAddEditForm/$1';
$route['check-duplicate-trade-code']        = SITEADMIN.'/Trades/CheckDuplicateTradeCode';
$route['check-duplicate-nqr-code']          = SITEADMIN.'/Trades/CheckDuplicateNqrCode';
$route['save-trade-nos']                    = SITEADMIN.'/Trades/save';
$route['delete-trade-nos/(:any)']           = SITEADMIN.'/Trades/delete/$1';
$route['get-mapped-trade-nos']              = SITEADMIN.'/Trades/ViewMappedTradeNos/$1';

/* Batches Menu  */
$route['list-batches-inprocess']            = SITEADMIN.'/Batches/inprocess';
$route['list-batches-ajax']                 = SITEADMIN.'/Batches/getLists';
$route['list-batches-completed']            = SITEADMIN.'/Batches/completed';
//$route['list-batches-completed-ajax'] = SITEADMIN.'/Batches/getCompletedLists';
$route['check-duplicate-batch-id']          = SITEADMIN.'/Batches/CheckDuplicateBatchId';
$route['create-batch']                      = SITEADMIN.'/Batches/viewAddEditForm';
$route['edit-batch/(:any)']                 = SITEADMIN.'/Batches/viewAddEditForm/$1';
$route['save-batch']                        = SITEADMIN.'/Batches/save';
$route['delete-batch/(:any)']               = SITEADMIN.'/Batches/delete/$1';
$route['get-trades-by-ssc']                 = SITEADMIN.'/Batches/GetTradesBySsc';
$route['get-partners-by-ssc']               = SITEADMIN.'/Batches/GetPartnersBySsc';
$route['get-assessors-by-ssc']              = SITEADMIN.'/Batches/GetAssessorsBySsc';
$route['get-centers-by-partner']            = SITEADMIN.'/Batches/GetCentersByPartner';
$route['import-students/(:any)']            = SITEADMIN.'/Batches/importStudents/$1';
$route['import-students-save']              = SITEADMIN.'/Batches/importStudentsSave';
$route['list-skipped-students-ajax']        = SITEADMIN.'/Batches/getSkippedStudentsLists';
$route['update-candidate-profile-verification-status']                  = SITEADMIN.'/Students/updateStudentProfileVerificationStatus';
$route['update-candidate-delete-status']                                = SITEADMIN.'/Students/updateStudentDeleteStatus';
$route['update-candidate-attendance-status']                            = SITEADMIN.'/Students/updateStudentAttendanceStatus';
$route['update-candidate-device-login-status']                          = SITEADMIN.'/Students/updateStudentDeviceLoginStatus';
$route['reset-exam']                                                    = SITEADMIN.'/Students/resetExam';
$route['generate-candidates-qp']                                        = SITEADMIN.'/Batches/generateCandidateQuestionBank'; 
$route['generate-question-bank']                                        = SITEADMIN.'/Batches/generateQuestionBank';
$route['download-batch-assessment-documents/(:any)/(:any)']             = SITEADMIN.'/Batches/downloadBatchAssessmentDocuments/$1/$2';
$route['view-batch-assessment-documents/(:any)/(:any)']                 = SITEADMIN.'/Batches/viewBatchAssessmentDocuments/$1/$2';
$route['download-batch-assessment-photos/(:any)/(:any)']                = SITEADMIN.'/Batches/downloadBatchAssessmentPhotos/$1/$2';
$route['download-batch-candidates-aadhaar-photos/(:any)/(:any)']        = SITEADMIN.'/Batches/downloadBatchCandidatesAadhaarPhotos/$1/$2';
$route['download-batch-candidates-with-aadhaar-photos/(:any)/(:any)']   = SITEADMIN.'/Batches/downloadBatchCandidatesWithAadhaarPhotos/$1/$2';
$route['download-batch-group-photos/(:any)/(:any)']                     = SITEADMIN.'/Batches/downloadBatchGroupPhotos/$1/$2';
$route['process-batch-result']                                          = SITEADMIN.'/Batches/processBatchResult';
$route['process-student-result']                                        = SITEADMIN.'/Batches/processStudentResult'; //Processing Single Candidate
$route['download-attendance-sheet/(:any)']                              = SITEADMIN.'/Attendance/GenerateAttendancePDF/$1';
$route['download-omr-sheet/(:any)']                                     = SITEADMIN.'/Omr/GenerateOmrPDF/$1';
$route['download-batch-basic-summary/(:any)']                           = SITEADMIN.'/Summary/GenerateBatchBasicSummaryPDF/$1';
$route['download-batch-detailed-summary/(:any)']                        = SITEADMIN.'/Summary/GenerateBatchDetailedSummaryPDF/$1';
$route['download-batch-question-paper/(:any)']                          = SITEADMIN.'/QuestionPaper/GenerateQuestionPaperPDF/$1';
$route['download-batch-answer-key/(:any)']                              = SITEADMIN.'/QuestionPaper/GenerateAnswerKeyPDF/$1';
$route['get-assessor-trades-by-ssc']                                    = SITEADMIN.'/Batches/GetAssessorTradesBySsc';
$route['download-batch-center-photos/(:any)']                           = SITEADMIN.'/Batches/downloadBatchCenterPhotos/$1';

/* Batches - Students */
$route['view-batch-students/(:any)'] = SITEADMIN.'/Students/view_batch_students/$1';
$route['list-batches-students-ajax'] = SITEADMIN.'/Students/getLists';
$route['view-candidate-assessment-page/(:any)/(:any)'] = SITEADMIN.'/Students/viewCandidateAssessmentPage/$1/$2';
$route['view-candidate-snapshots/(:any)'] = SITEADMIN.'/Students/viewCandidateSnapshots/$1';


/* Assessors Menu  */
$route['list-assessors'] = SITEADMIN.'/Assessors/list';
$route['list-assessors-ajax'] = SITEADMIN.'/Assessors/getLists';
$route['create-assessor'] = SITEADMIN.'/Assessors/viewAddEditForm';
$route['edit-assessor/(:any)'] = SITEADMIN.'/Assessors/viewAddEditForm/$1';
$route['save-assessor'] = SITEADMIN.'/Assessors/save';
$route['delete-assessor/(:any)'] = SITEADMIN.'/Assessors/delete/$1';
$route['reset-assessor-device/(:any)'] = SITEADMIN.'/Assessors/resetDevice/$1';
$route['check-duplicate-assessor-code'] = SITEADMIN.'/Assessors/CheckDuplicateAssessorCode';
$route['check-duplicate-assessor-mobile'] = SITEADMIN.'/Assessors/CheckDuplicateAssessorMobile';
$route['check-duplicate-assessor-email'] = SITEADMIN.'/Assessors/CheckDuplicateAssessorEmail';

/* Question Bank Menu  */
$route['list-questions'] = SITEADMIN.'/questions/list_questions';
$route['import-questionbank'] = SITEADMIN.'/Questions/import';
$route['import-questions-save'] = SITEADMIN.'/Questions/importQuestionsSave';
$route['list-skipped-questions-ajax'] = SITEADMIN.'/questions/getSkippedQuestionsLists';
$route['get-select-nos'] = SITEADMIN.'/questions/getSelectNos';
$route['get-questions'] = SITEADMIN.'/questions/getQuestions';
$route['get-question-type-counts'] = SITEADMIN.'/questions/getQuestionTypeCounts';
$route['get-practical-activity-questions'] = SITEADMIN.'/questions/getPracticalActivityQuestions';
$route['get-viva-questions'] = SITEADMIN.'/questions/getVivaQuestions';
$route['delete-question'] = SITEADMIN.'/questions/delete';
$route['download-ques'] = SITEADMIN.'/questions/download_questions';
$route['edit-question/(:any)'] = SITEADMIN.'/questions/edit_question/$1';
$route['save-question'] = SITEADMIN.'/questions/save';
$route['edit-questions'] = SITEADMIN.'/Questions/edit_questions';

/* SectorSkillCouncil :: List/Add/Edit/Delete */
$route['list-sectorskillcouncils']                  = SITEADMIN.'/SectorSkillCouncil/list';
$route['save-sectorskillcouncil']                   = SITEADMIN.'/SectorSkillCouncil/save';
$route['delete-sectorskillcouncil/(:any)']          = SITEADMIN.'/SectorSkillCouncil/delete/$1';

$route['question-upload']                           = SITEADMIN.'/Import/importQuestions';

$route['search-results/(:any)']                     = SITEADMIN.'/Results/search_results/$1';
$route['view-batch-result/(:any)']                  = SITEADMIN.'/Results/view_batch_result/$1';
$route['view-result-summary/(:any)/(:any)']         = SITEADMIN.'/Results/view_result_summary/$1/$2';
$route['view-batch-result-summary/(:any)/(:any)']   = SITEADMIN.'/Results/view_batch_result_summary/$1/$2'; 
$route['get-results-trades-by-ssc']                 = SITEADMIN.'/Results/GetTradesBySsc';
$route['get-batch-by-ssc-trade']                    = SITEADMIN.'/Results/GetBatchBySscTrade';
$route['get-candidate-by-batch']                    = SITEADMIN.'/Results/GetCandidateByBatch';
$route['list-results-ajax']                         = SITEADMIN.'/Results/getLists';
$route['download-percentage-sheet/(:any)']          = SITEADMIN.'/Results/DownloadPercentageSheet/$1';
$route['download-NOS-result-sheet/(:any)']          = SITEADMIN.'/Results/DownloadNOSresultSheet/$1';

$route['moderate-results/(:any)']   = SITEADMIN.'/Moderation/moderate_results/$1';
$route['save-theory-moderation']    = SITEADMIN.'/Moderation/save_theory_moderation';
$route['save-pa-moderation']        = SITEADMIN.'/Moderation/save_pa_moderation';
$route['save-viva-moderation']      = SITEADMIN.'/Moderation/save_viva_moderation';

/* Dashboard */
$route['get-dashboard-partners-count']                          = SITEADMIN.'/Dashboard/get_partners_count';
$route['get-dashboard-centers-count']                           = SITEADMIN.'/Dashboard/get_centers_count';
$route['get-dashboard-batch-count']                             = SITEADMIN.'/Dashboard/get_batch_count';
$route['get-dashboard-assessors-count']                         = SITEADMIN.'/Dashboard/get_assessors_count';
$route['get-dashboard-students-asssessment-pending-count']      = SITEADMIN.'/Dashboard/get_students_assessment_pending_count';
$route['get-dashboard-students-asssessment-completed-count']    = SITEADMIN.'/Dashboard/get_students_assessment_completed_count';
$route['get-dashboard-asssessment-pending-count']               = SITEADMIN.'/Dashboard/get_batch_inprocess_count'; 
$route['get-dashboard-asssessment-completed-count']             = SITEADMIN.'/Dashboard/get_batch_completed_count';
$route['get-dashboard-batch-results-pending-count']             = SITEADMIN.'/Dashboard/get_batch_results_pending_count';
$route['get-dashboard-batch-results-completed-count']           = SITEADMIN.'/Dashboard/get_batch_results_completed_count';
$route['get-dashboard-batches-review-count']                    = SITEADMIN.'/Dashboard/get_batch_review_count';

/* Export */
$route['export-assessors']                                                      = SITEADMIN.'/Export/export_assessors';
$route['export-batches-inprocess/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)']    = SITEADMIN.'/Export/export_batches/$1/$2/$3/$4/$5/$6';
$route['export-batches-completed/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)']    = SITEADMIN.'/Export/export_batches/$1/$2/$3/$4/$5/$6'; 
$route['export-expenses-inprocess/(:any)/(:any)/(:any)/(:any)']                 = SITEADMIN.'/Export/export_expense_report/$1/$2/$3/$4';  
$route['export-expenses-paid-rejected/(:any)/(:any)/(:any)/(:any)']             = SITEADMIN.'/Export/export_expense_report/$1/$2/$3/$4'; 

/* Expenses Menu  */
$route['list-pending-expenses']         = SITEADMIN.'/Expenses/inprocess';
$route['list-expenses-ajax']            = SITEADMIN.'/Expenses/getLists';
$route['list-paid-rejected-expenses']   = SITEADMIN.'/Expenses/completed';
$route['get-expense-details']           = SITEADMIN.'/Expenses/ViewExpenseDetails';
$route['save-expense-status']           = SITEADMIN.'/Expenses/saveExpenseStatus';

/* Cron to Watermark the video recordings */
$route['watermark-video-recording']     = SITEADMIN.'/Cron/watermarkVideoRecording';

/* CMS Modules & Permissions Menu :: List/Add/Edit/Delete  */
$route['list-modules']                      = SITEADMIN.'/Cms_modules/list'; 
$route['save-module']                       = SITEADMIN.'/Cms_modules/save';
$route['delete-module/(:any)']              = SITEADMIN.'/Cms_modules/delete_module/$1';
$route['check-duplicate-module']            = SITEADMIN.'/Cms_modules/CheckDuplicateModule';

$route['list-module-permissions/(:any)']    = SITEADMIN.'/Cms_modules/modulePermissionsList/$1';
$route['save-module-permission']            = SITEADMIN.'/Cms_modules/saveModulePermission';
$route['delete-permission/(:any)']          = SITEADMIN.'/Cms_modules/delete_permission/$1';
$route['check-duplicate-permission']        = SITEADMIN.'/Cms_modules/CheckDuplicatePermission';

/* Roles & Permissions Menu :: List/Add/Edit/Delete  */
$route['list-roles']                        = SITEADMIN.'/Roles/list';
$route['save-role']                         = SITEADMIN.'/Roles/save';
$route['delete-role/(:any)']                = SITEADMIN.'/Roles/delete_role/$1';
$route['check-duplicate-role']              = SITEADMIN.'/Roles/CheckDuplicateRole';

$route['list-roles-permissions/(:any)']     = SITEADMIN.'/Roles/rolesPermissionsList/$1';
$route['save-role-permissions']             = SITEADMIN.'/Roles/savePermissions';
$route['assign-superadmin-permissions']     = SITEADMIN.'/Roles/saveSuperadminPermissions';

$route['list-users']                        = SITEADMIN.'/User/userList';
$route['edit_user_details']                 = SITEADMIN.'/User/edit_user_details';
$route['save-user']                         = SITEADMIN.'/User/save';
$route['delete-user-details/(:any)']        = SITEADMIN.'/User/delete_user_details/$1';
$route['check-duplicate-user-email']        = SITEADMIN.'/User/CheckDuplicateUserEmail';

$route['cron_generate_permissions']         = SITEADMIN.'/cron_generate_permissions/index';
$route['assign_superadmin_permissions']     = SITEADMIN.'/cron_generate_permissions/assignPermissionToSuperAdmin';

$route['change-password']                   = SITEADMIN.'/Usermanagement/change_password';
$route['submit-change-password']            = SITEADMIN.'/Usermanagement/submit_change_password';

$route['cron_delete_assessment_data']       = SITEADMIN.'/cron_delete_assessment_data/index';
$route['cron_delete_batch']                 = SITEADMIN.'/cron_delete_assessment_data/deleteBatch'; 

/*--------------------------------- End Admin Routes-------------------------------------------------*/

/*--------------------------------- Candidate Web Routes-------------------------------------------------*/

$route['candidate-login']           = SITECANDIDATE.'/Login/index';
$route['candidate-validate-login']  = SITECANDIDATE.'/Login/validate';
$route['candidate-logout']          = SITECANDIDATE.'/Login/logout';
$route['candidate-dashboard']       = SITECANDIDATE.'/Login/dashboard';
$route['candidate-profile']         = SITECANDIDATE.'/Login/updateprofile';
$route['save-candidate-profile']    = SITECANDIDATE.'/Login/saveProfile';
$route['assessment-page']           = SITECANDIDATE.'/Exam/candidateassessment';


/*--------------------------------- End Candidate Web Routes-------------------------------------------------*/

/*--------------------------------- Candidate Apis Routes-------------------------------------------------*/

$route['Api-Candidate-Login']                       =   CANDIDATEAPI.'/login';
$route['Api-Candidate-Logout']                      =   CANDIDATEAPI.'/logout';
$route['Api-Update-Candidate-Profile']              =   CANDIDATEAPI.'/updateCandidateProfile';
$route['Api-Update-Candidate-Profile-Web']          =   CANDIDATEAPI.'/updateCandidateProfileWeb';
$route['Api-View-Candidate-Profile']                =   CANDIDATEAPI.'/getCandidateProfile';
$route['Api-Dashboard']                             =   CANDIDATEAPI.'/dashboard';
$route['Api-State-District-List']                   =   CANDIDATEAPI.'/getStateDistrictList';
$route['Api-Questions-List']                        =   CANDIDATEAPI.'/getCandidateQuestionsList';
$route['Api-Save-Answer']                           =   CANDIDATEAPI.'/saveCandidateAnswer';
$route['Api-Submit-Theory']                         =   CANDIDATEAPI.'/submitTheoryExam';
$route['Api-Palette-List']                          =   CANDIDATEAPI.'/getPaletteList';
$route['Api-View-Question-Details']                 =   CANDIDATEAPI.'/getQuestionDetails';
$route['Api-Save-Snapshot']                         =   CANDIDATEAPI.'/saveCandidateSnapshot';
$route['Api-Save-Profile-Image']                    =   CANDIDATEAPI.'/saveCandidateProfileImage';
$route['Api-Practical-Activity-Questions-List']     =   CANDIDATEAPI.'/getCandidatePracticalActivityQuestionsList';
$route['Api-Viva-Questions-List']                   =   CANDIDATEAPI.'/getCandidateVivaQuestionsList';
$route['Api-Practical-Activity-Viva-List']          =   CANDIDATEAPI.'/getCandidatePracticalActivityVivaQuestionsList';
$route['Api-Single-Questions-List']                 =   CANDIDATEAPI.'/getCandidateSingleQuestionsList';

/*---------------------------------End Candidate Apis---------------------------------------------*/

/*--------------------------------- Assessor Apis Routes-------------------------------------------------*/

$route['Api-Assessor-Login']                       =   ASSESSORAPI.'/login';
$route['Api-Assessor-Logout']                      =   ASSESSORAPI.'/logout';
$route['Api-Update-Assessor-Profile']              =   ASSESSORAPI.'/updateAssessorProfile';
$route['Api-View-Assessor-Profile']                =   ASSESSORAPI.'/getAssessorProfile';
$route['Api-Save-Assessor-Profile-Image']          =   ASSESSORAPI.'/saveAssessorProfileImage';
$route['Api-Assessor-Dashboard']                   =   ASSESSORAPI.'/dashboard';
$route['Api-Assessor-State-District-List']         =   ASSESSORAPI.'/getStateDistrictList';
$route['Api-Save-Assessor-Assessment-Images']      =   ASSESSORAPI.'/saveAssessorAssessmentImage';
$route['Api-Batch-Assessment-Candidates-List']     =   ASSESSORAPI.'/batchAssessmentCandidateList';
$route['Api-Save-Candidate-Attendance-Status']     =   ASSESSORAPI.'/saveCandidateAttendanceStatus';
$route['Api-Practical-Activity-Viva-Questions']    =   ASSESSORAPI.'/getCandidatePracticalActivityVivaQuestionsList';
$route['Api-Theory-Questions']                     =   ASSESSORAPI.'/getCandidateTheoryQuestionsList';
$route['Api-Save-Candidate-Recording']             =   ASSESSORAPI.'/saveCandidateRecording';
$route['Api-Save-Candidate-Recording-Marks']       =   ASSESSORAPI.'/saveCandidateRecordingMarks';
$route['Api-Assessor-Checklist-Documents-List']    =   ASSESSORAPI.'/getChecklistDocuments'; 
$route['Api-Save-Assessor-Checklist-Documents']    =   ASSESSORAPI.'/saveAssessorChecklistDocuments';
$route['get-assessor-resume-upload-form/(:any)']   =   ASSESSORAPI.'/getResumeUploadForm/$1';
$route['save-resume-upload']                       =   ASSESSORAPI.'/saveResumeUpload';
$route['Api-Save-Resume-Upload']                   =   ASSESSORAPI.'/saveResumeUploadData';
$route['Api-Assessor-Sectors-List']                =   ASSESSORAPI.'/getSectorsList';
$route['Api-Save-Mapped-Sectors']                  =   ASSESSORAPI.'/saveMappedSectors';
$route['Api-Assessor-Associated-Agencies-List']    =   ASSESSORAPI.'/getAssociatedAgenciesList';
$route['Api-Save-Associated-Agencies']             =   ASSESSORAPI.'/saveAssociatedAgencies';
$route['Api-Save-Candidate-Assessment-Status']     =   ASSESSORAPI.'/saveCandidateAssessmentStatus';
$route['Api-Save-Batch-Assessment-Status']         =   ASSESSORAPI.'/saveBatchAssessmentStatus';
$route['Api-Download-Checklist-Documents-List']    =   ASSESSORAPI.'/downloadChecklistDocuments'; 
$route['Api-Save-Candidate-Theory-Marks']          =   ASSESSORAPI.'/saveCandidateTheoryMarks';
$route['Api-Save-Batch-Expense']                   =   ASSESSORAPI.'/saveBatchExpense';
$route['Api-Save-Batch-Other-Expense']             =   ASSESSORAPI.'/saveBatchOtherExpense';
$route['Api-Assessor-Batch-Expense-List']          =   ASSESSORAPI.'/getAssessorBatchExpenseList';
$route['Api-Delete-Batch-Expense']                 =   ASSESSORAPI.'/deleteBatchExpense';
$route['Api-Create-Batch-Expense-Detail-Id']       =   ASSESSORAPI.'/createBatchExpenseDetailId';
$route['Api-Save-Batch-Expense-File']              =   ASSESSORAPI.'/saveBatchExpenseFile';
$route['Api-Submit-Batch-Expense']                 =   ASSESSORAPI.'/submitBatchExpense'; 

$route['Api-get-user-id']                          =   ASSESSORAPI.'/getEncUserID';

/*---------------------------------End Assessor Apis---------------------------------------------*/





