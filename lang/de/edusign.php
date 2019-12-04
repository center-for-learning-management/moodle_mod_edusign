<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'edusignment', language 'de', branch 'MOODLE_35_STABLE'
 *
 * @package   edusignment
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activityoverview'] = 'Sie haben eine Unterschrift, die Ihre Bearbeitung erfordern.';
$string['addattempt'] = 'Weiteren Versuch zulassen';
$string['addnewattempt'] = 'Neuen Versuch hinzufügen';
$string['addnewattemptfromprevious'] = 'Neuen Versuch auf Grundlage der vorherigen Lösung abgeben';
$string['addnewattemptfromprevious_help'] = 'Hiermit kopieren Sie den Inhalt Ihrer vorherigen Lösung, um eine neue Lösung zu erstellen.';
$string['addnewattempt_help'] = 'Dies erzeugt eine neue leere Lösung, die Sie bearbeiten können.';
$string['addnewgroupoverride'] = 'Verfügbarkeitsänderungen für Gruppen anlegen';
$string['addnewuseroverride'] = 'Verfügbarkeitsänderung für Nutzer anlegen';
$string['addsubmission'] = 'Abgabe hinzufügen';
$string['addsubmission_help'] = 'Sie haben bisher keine Lösung abgegeben.';
$string['allocatedmarker'] = 'Zugeordnete/r Bewerter/in';
$string['allocatedmarker_help'] = 'Bewerter/in, der/die dieser Abgabe zugeordnet ist';
$string['allowsubmissions'] = 'Nutzer/in erlauben, für diese Aufgabe weiter Lösungen abzugeben';
$string['allowsubmissionsanddescriptionfromdatesummary'] = 'Die Aufgabendetails und die Lösungsabgabe stehen zur Verfügung ab <strong>{$a}</strong>';
$string['allowsubmissionsfromdate'] = 'Abgabebeginn';
$string['allowsubmissionsfromdate_help'] = 'Wenn diese Option aktiviert ist, können Lösungen nicht vor diesem Zeitpunkt abgegeben werden. Wenn diese Option deaktiviert ist, ist die Abgabe sofort möglich.';
$string['allowsubmissionsfromdatesummary'] = 'Abgabe möglich ab <strong>{$a}</strong>';
$string['allowsubmissionsshort'] = 'Abgabeänderung erlauben';
$string['alwaysshowdescription'] = 'Beschreibung immer anzeigen';
$string['alwaysshowdescription_help'] = 'Wenn diese Option deaktiviert ist, wird die Aufgabenbeschreibung für Teilnehmer/innen nur ab dem Abgabebeginn angezeigt.';
$string['applytoteam'] = 'Bewertungen und Feedback der gesamten Gruppe zuweisen.';
$string['edusignment:addinstance'] = 'Neue Aufgabe hinzufügen';
$string['edusignment:editothersubmission'] = 'Weitere Teilnehmerlösung bearbeiten';
$string['edusignment:exportownsubmission'] = 'Eigene Lösung exportieren';
$string['edusignmentfeedback'] = 'Feedback Plugin';
$string['edusignmentfeedbackpluginname'] = 'Feedback Plugin';
$string['edusignment:grade'] = 'Aufgabe bewerten';
$string['edusignment:grantextension'] = 'Erweiterung zulassen';
$string['edusignment:manageallocations'] = 'Bewerter/innen verwalten, die dieser Abgabe zugeordnet sind';
$string['edusignment:managegrades'] = 'Bewertungen überprüfen und veröffentlichen';
$string['edusignment:manageoverrides'] = 'Verfügbarkeitsänderungen für Aufgaben verwalten';
$string['edusignmentisdue'] = 'Das Abgabeende ist vorbei';
$string['edusignmentmail'] = 'Guten Tag,

{$a->grader} hat Ihnen ein Feedback zur Ihrer Aufgabenlösung für  \'{$a->edusignment}\' bereitgestellt.

Mit dem folgenden Link können Sie direkt darauf zugreifen:

{$a->url}

Ihr E-Learning-Team';
$string['edusignmentmailhtml'] = 'Guten Tag,

<p>{$a->grader} hat Ihnen ein Feedback zur Ihrer Aufgabenlösung für \'<i>{$a->edusignment}</i>\' bereitgestellt.</p> <p>Mit dem folgenden Link können Sie direkt darauf zugreifen: <a href="{$a->url}">Link zu Ihrer Lösung und zum Feedback</a>.</p>

Ihr E-Learning-Team';
$string['edusignmentmailsmall'] = 'Guten Tag,

{$a->grader} hat Ihnen ein Feedback zur Ihrer Aufgabenlösung für  \'{$a->edusignment}\' bereitgestellt. Mit dem folgenden Link können Sie direkt darauf zugreifen: {$a->url}

Ihr E-Learning-Team';
$string['edusignmentname'] = 'Name der eduSign Abgabe';
$string['edusignmentplugins'] = 'Aufgabentypen';
$string['edusignmentsperpage'] = 'Unterschriften pro Seite';
$string['edusignment:receivegradernotifications'] = 'Systemnachrichten zur Bewertungsabgabe empfangen';
$string['edusignment:releasegrades'] = 'Bewertung veröffentlichen';
$string['edusignment:revealidentities'] = 'Teilnehmeridentität anzeigen';
$string['edusignment:reviewgrades'] = 'Bewertungen prüfen';
$string['edusignmentsubmission'] = 'Abgabetyp';
$string['edusignmentsubmissionpluginname'] = 'Abgabetyp';
$string['edusignment:submit'] = 'Aufgabe unterschrieben';
$string['edusignment:view'] = 'Unterschrift ansehen';
$string['edusignment:viewblinddetails'] = 'Teilnehmeridentität anzeigen, obwohl anonyme Bewertung eingeschaltet ist';
$string['edusignment:viewgrades'] = 'Bewertungen anzeigen';
$string['attemptheading'] = 'Versuch {$a->attemptnumber}: {$a->submissionsummary}';
$string['attempthistory'] = 'Vorherige Versuche';
$string['attemptnumber'] = 'Nummer';
$string['attemptreopenmethod'] = 'Versuche erneut bearbeitbar';
$string['attemptreopenmethod_help'] = 'Die Option legt fest, ob Teilnehmer/innen die eingereichten Versuche erneut zum Bearbeiten öffnen dürfen. Mögliche Optionen sind: <ul><li>Nie - Die Lösung kann nicht erneut bearbeitet werden.</li><li>Manuell - Ein/e Trainer/in kann das erneute Bearbeiten zulassen.</li><li>Automatisch bis zum Bestehen - Die Lösung kann solange bearbeitet werden, bis die Voraussetzung zum Bestehen erfüllt sind (Eintrag in Bewertungen - Kategorien und Aspekte).</li></ul>';
$string['attemptreopenmethod_manual'] = 'Manuell';
$string['attemptreopenmethod_none'] = 'Nie';
$string['attemptreopenmethod_untilpass'] = 'Automatisch bis zum Bestehen';
$string['attemptsettings'] = 'Einstellungen für Versuche';
$string['availability'] = 'Verfügbarkeit';
$string['backtoedusignment'] = 'Zurück zur Aufgabe';
$string['batchoperationconfirmaddattempt'] = 'Einen weiteren Versuch für ausgewählte Lösungen erlauben?';
$string['batchoperationconfirmdownloadselected'] = 'Ausgewählte Abgaben herunterladen?';
$string['batchoperationconfirmgrantextension'] = 'Abgabeende für die ausgewählten Abgaben verlängern?';
$string['batchoperationconfirmlock'] = 'Ausgewählte Abgaben sperren?';
$string['batchoperationconfirmreverttodraft'] = 'Ausgewählte Abgaben in den Entwurfsmodus zurücksetzen?';
$string['batchoperationconfirmsetmarkingallocation'] = 'Bewerter-Zuordnung für alle ausgewählten Einreichungen setzen?';
$string['batchoperationconfirmsetmarkingworkflowstate'] = 'Bewertungsworkflow-Status für alle ausgewählten Einreichungen setzen?';
$string['batchoperationconfirmunlock'] = 'Ausgewählte Abgaben freigeben?';
$string['batchoperationlock'] = 'Abgaben sperren';
$string['batchoperationreverttodraft'] = 'Abgaben in den Entwurfsmodus zurücksetzen';
$string['batchoperationsdescription'] = 'Mit Auswahl...';
$string['batchoperationunlock'] = 'Abgaben freigeben';
$string['batchsetallocatedmarker'] = 'Bewerter für {$a} ausgewählte Nutzer festlegen.';
$string['batchsetmarkingworkflowstateforusers'] = 'Bewertungsworkflow-Status für {$a} ausgewählte Nutzer festlegen.';
$string['blindmarking'] = 'Anonyme Bewertung';
$string['blindmarkingenabledwarning'] = 'Anonyme Bewertungen sind für diese Aktivität aktiviert.';
$string['blindmarking_help'] = 'Die anonyme Bewertung verbirgt die Teilnehmeridentität während der Bewertung. Die Option kann nicht mehr geändert werden, nachdem die erste Lösung eingereicht oder Bewertung vorgenommen wurde.';
$string['calendardue'] = '{$a} ist fällig.';
$string['changefilters'] = 'Filter wechseln';
$string['changegradewarning'] = 'In dieser Aufgabe sind bereits Lösungen bewertet worden. Bei einer Änderung der Bewertungsskala sind Neuberechnungen der Bewertungen erforderlich. Sie müssen ggfs. die Neuberechnung gesondert starten.';
$string['changeuser'] = 'Nutzer/in wechseln';
$string['choosegradingaction'] = 'Bewertungsvorgang';
$string['choosemarker'] = 'Auswählen...';
$string['chooseoperation'] = 'Operation wählen';
$string['clickexpandreviewpanel'] = 'Klicken, um die Überprüfungsansicht aufzuklappen';
$string['collapsegradepanel'] = 'Bewertungsansicht einklappen';
$string['collapsereviewpanel'] = 'Überprüfungsansicht einklappen';
$string['comment'] = 'Kommentar';
$string['completionsubmit'] = 'Teilnehmer/in muss Lösung eingereicht haben, um Aktivität abzuschließen';
$string['configshowrecentsubmissions'] = 'Alle können Nachrichten zur Aufgabenabgabe im Aktivitätenbericht sehen';
$string['confirmbatchgradingoperation'] = 'Möchten Sie für {$a->count} Nutzer/innen die Operation {$a->operation} ausführen?';
$string['confirmsubmission'] = 'Wenn Sie nun Ihre Lösung zur Bewertung einreichen, können Sie keine Änderungen mehr vornehmen. Sind Sie sich sicher?';
$string['confirmsubmissionheading'] = 'Abgabe bestätigen';
$string['conversionexception'] = 'Die Aufgabe konnte nicht konvertiert werden. {$a}';
$string['couldnotconvertgrade'] = 'Die Aufgabenbewertung für \'{$a}\' konnte nicht konvertiert werden.';
$string['couldnotconvertsubmission'] = 'Die Aufgabenabgabe für \'{$a}\' konnte nicht konvertiert werden.';
$string['couldnotcreatecoursemodule'] = 'Das Kursmodul konnte nicht angelegt werden.';
$string['couldnotcreatenewedusignmentinstance'] = 'Die neue Aufgabeninstanz konnte nicht angelegt werden.';
$string['couldnotfindedusignmenttoupgrade'] = 'Die alte Aufgabeninstanz konnte nicht gefunden werden, um sie zu aktualisieren.';
$string['currentattempt'] = 'Dies ist Versuch {$a}.';
$string['currentattemptof'] = 'Versuch {$a->attemptnumber} (mögliche Versuche {$a->maxattempts})';
$string['currentgrade'] = 'Aktuelle Bewertung in Bewertungen';
$string['cutoffdate'] = 'Letzte Abgabemöglichkeit';
$string['cutoffdatecolon'] = 'Letzte Abgabemöglichkeit: {$a}';
$string['cutoffdatefromdatevalidation'] = 'Der Termin der letzten Abgabemöglichkeit kann nicht früher liegen als der erlaubte Abgabebeginn.';
$string['cutoffdate_help'] = 'Diese Funktion sperrt die Abgabe von Lösungen ab diesem Termin, sofern keine individuellen Verlängerungen zugelassen wurden.';
$string['cutoffdatevalidation'] = 'Der Termin der letzten Abgabemöglichkeit kann nicht früher liegen als das Fälligkeitsdatum.';
$string['defaultlayout'] = 'Standardlayout wiederherstellen';
$string['defaultsettings'] = 'Standardmäßige Voreinstellungen';
$string['defaultsettings_help'] = 'Diese Einstellungen legen Vorgaben für alle neuen Aufgaben fest.';
$string['defaultteam'] = 'Standard-Gruppe';
$string['deleteallsubmissions'] = 'Alle Unterschriften löschen';
$string['description'] = 'Beschreibung';
$string['disabled'] = 'Deaktiviert';
$string['downloadall'] = 'Alle Unterschriften herunterladen';
$string['downloadasfolders'] = 'Unterschriften in Verzeichnissen herunterladen';
$string['downloadasfolders_help'] = 'Bei Aktivierung werden die heruntergeladenen Dateien in separaten Ordnern platziert und Dateien werden nicht umbenannt.';
$string['downloadselectedsubmissions'] = 'Ausgewählte Abgaben herunterladen';
$string['duedate'] = 'Fälligkeitsdatum';
$string['duedatecolon'] = 'Fälligkeitsdatum: {$a}';
$string['duedate_help'] = 'Zum Abgabeende wird die Aufgabe fällig. Spätere Abgaben sind auch danach noch möglich, werden dann aber als verspätet markiert. Um eine Abgabe nach einem bestimmten Datum zu verhindern, kann ein Termin der letzten Abgabemöglichkeit gesetzt werden.';
$string['duedateno'] = 'Kein Fälligkeitsdatum';
$string['duedatereached'] = 'Das Fälligkeitsdatum für diese Aufgabe ist vorbei.';
$string['duedatevalidation'] = 'Das Fälligkeitsdatum kann nicht früher liegen als der erlaubte Abgabebeginn.';
$string['duplicateoverride'] = 'Überschreibung duplizieren';
$string['editaction'] = 'Aktivitäten...';
$string['editattemptfeedback'] = 'Bewertung und Feedback für Versuch {$a} bearbeiten';
$string['editingpreviousfeedbackwarning'] = 'Sie bearbeiten das Feedback für einen vorherigen Versuch. Dies ist Versuch {$a->attemptnumber} von {$a->totalattempts}.';
$string['editingstatus'] = 'Bearbeitungsstatus';
$string['editonline'] = 'Online bearbeiten';
$string['editoverride'] = 'Überschreibung bearbeiten';
$string['editsubmission'] = 'Unterschrift bearbeiten';
$string['editsubmission_help'] = 'Sie können Ihre Unterschrift noch verändern.';
$string['editsubmissionother'] = 'Unterschrift bearbeiten für {$a}';
$string['enabled'] = 'Aktiviert';
$string['errornosubmissions'] = 'Keine Abgaben zum Herunterladen verfügbar';
$string['errorquickgradingvsadvancedgrading'] = 'Die Aufgabe verwendet das erweiterte Bewertungsschema. Daher werden diese Bewertungen nun nicht abgespeichert.';
$string['errorrecordmodified'] = 'Bevor Sie die Seite aufgerufen haben, hat jemand anders einen oder mehrere Daten geändert. Deswegen können Ihre Einträge nun nicht gesichert werden.';
$string['eventallsubmissionsdownloaded'] = 'Alle abgegebenen Lösungen wurden heruntergeladen.';
$string['eventassessablesubmitted'] = 'Eine Lösung wurde abgegeben.';
$string['eventbatchsetmarkerallocationviewed'] = 'Bewerter-Zuordnung angezeigt';
$string['eventbatchsetworkflowstateviewed'] = 'Bewertungsworkflow-Batch angezeigt';
$string['eventextensiongranted'] = 'Eine Verlängerung wurde gewährt.';
$string['eventfeedbackupdated'] = 'Feedback aktualisiert';
$string['eventfeedbackviewed'] = 'Feedback angezeigt';
$string['eventgradingformviewed'] = 'Bewertungsformular angezeigt';
$string['eventgradingtableviewed'] = 'Bewertungstabelle angezeigt';
$string['eventidentitiesrevealed'] = 'Die Identitäten wurden aufgedeckt.';
$string['eventmarkerupdated'] = 'Zugewiesene/r Bewerter/in wurde aktualisiert.';
$string['eventoverridecreated'] = 'Verfügbarkeitsänderung für Aufgaben angelegt';
$string['eventoverridedeleted'] = 'Verfügbarkeitsänderungen für Aufgaben gelöscht';
$string['eventoverrideupdated'] = 'Verfügbarkeitsänderungen für Aufgaben bearbeitet';
$string['eventrevealidentitiesconfirmationpageviewed'] = 'Identität der Bewerter wurde sichtbar gemacht';
$string['eventstatementaccepted'] = 'Nutzer/in hat die Abgabebedingung bestätigt.';
$string['eventsubmissionconfirmationformviewed'] = 'Abgabebestätigungsformular angesehen';
$string['eventsubmissioncreated'] = 'Abgabe angelegt';
$string['eventsubmissionduplicated'] = 'Die Lösung wurde von Nutzer/in dupliziert';
$string['eventsubmissionformviewed'] = 'Abgabeformular angezeigt';
$string['eventsubmissiongraded'] = 'Die Lösung wurde bewertet.';
$string['eventsubmissionlocked'] = 'Die Abgabe wurde für Nutzer/in gesperrt.';
$string['eventsubmissionstatusupdated'] = 'Der Abgabestatus wurde aktualisiert.';
$string['eventsubmissionstatusviewed'] = 'Abgabestatus angezeigt';
$string['eventsubmissionunlocked'] = 'Die Abgabe wurde für Nutzer/in freigeschaltet.';
$string['eventsubmissionupdated'] = 'Nutzer/in hat Lösung gesichert.';
$string['eventsubmissionviewed'] = 'Abgabe angezeigt';
$string['eventworkflowstateupdated'] = 'Der Workflow-Status wurde aktualisiert.';
$string['expandreviewpanel'] = 'Überprüfungsansicht ausklappen';
$string['extensionduedate'] = 'Verlängerung des Fälligkeitsdatums';
$string['extensionnotafterduedate'] = 'Das verlängerte Fälligkeitsdatum muss nach dem (normalen) Fälligkeitsdatum liegen.';
$string['extensionnotafterfromdate'] = 'Das verlängerte Abgabeende muss nach dem Abgabebeginn liegen.';
$string['feedback'] = 'Feedback';
$string['feedbackavailablehtml'] = '{$a->username} hat Ihnen ein Feedback zu Ihrer Abgabe für \'<i>{$a->edusignment}</i>\' bereitgestellt.<br /><br /> Mit dem folgenden Link können Sie direkt darauf zugreifen: <a href="{$a->url}">Link zu Ihrer Lösung und zum Feedback</a>.';
$string['feedbackavailablesmall'] = '{$a->username} hat Ihnen für Ihre Lösung bei \'{$a->edusignment}\' ein Feedback gegeben.';
$string['feedbackavailabletext'] = '{$a->username} hat Ihnen ein Feedback zu Ihrer Abgabe für \'{$a->edusignment}\' bereitgestellt. Mit dem folgenden Link können Sie direkt darauf zugreifen: {$a->url}';
$string['feedbackplugin'] = 'Feedback Plugin';
$string['feedbackpluginforgradebook'] = 'Plugin zur Übertragung von Feedback in den Bewertungsbereich';
$string['feedbackpluginforgradebook_help'] = 'Nur eine Bewertung kann in den Bewertungsbereich des Kurses übertragen werden.';
$string['feedbackplugins'] = 'Feedback Plugins';
$string['feedbacksettings'] = 'Feedback';
$string['feedbacktypes'] = 'Feedback-Typen';
$string['filesubmissions'] = 'Dateiabgaben';
$string['filter'] = 'Filter';
$string['filtergrantedextension'] = 'Gewährte Verlängerung';
$string['filternone'] = 'Kein Filter';
$string['filternotsubmitted'] = 'Nicht abgegeben';
$string['filterrequiregrading'] = 'Bewertung notwendig';
$string['filtersubmitted'] = 'Abgegeben';
$string['fixrescalednullgrades'] = 'Die Aufgabe enthält fehlerhafte Bewertungen. Sie können die <a href="{$a->link} ">Bewertungen automatisch beheben</a>. Die Kurssummen könnten dabei beeinflusst werden.';
$string['fixrescalednullgradesconfirm'] = 'Möchten Sie die fehlerhaften Bewertungen automatisch beheben lassen? Alle betroffenen Bewertungen werden entfernt. Die Kurssummen könnten beeinflusst werden.';
$string['fixrescalednullgradesdone'] = 'Bewertungen korrigiert';
$string['gradeabovemaximum'] = 'Bewertung muss kleiner oder gleich {$a} sein.';
$string['gradebelowzero'] = 'Bewertung muss größer oder gleich Null sein.';
$string['gradecanbechanged'] = 'Bewertung kann geändert werden';
$string['gradechangessaveddetail'] = 'Die Änderungen für Bewertung und Feedback wurden gesichert.';
$string['graded'] = 'Bewertet';
$string['gradedby'] = 'Bewertet von';
$string['gradedon'] = 'Bewertet am';
$string['gradelocked'] = 'Diese Bewertung ist gesperrt oder wurde im Bewertungsbereich überschrieben.';
$string['gradeoutof'] = 'Bewertung (max. {$a})';
$string['gradeoutofhelp'] = 'Bewertung';
$string['gradeoutofhelp_help'] = 'Geben Sie hier die Bewertung für die Aufgabenlösung ein. Es können Dezimalwerte eingetragen werden.';
$string['gradersubmissionupdatedhtml'] = '{$a->username} hat die Aufgabe <i>\'{$a->edusignment}\'</i> bearbeitet und am {$a->timeupdated} hochgeladen. <br /><br />
Die Abgabe ist <a href="{$a->url}">auf der Website verfügbar</a>.';
$string['gradersubmissionupdatedsmall'] = '{$a->username} hat die eingereichte Lösung zur Aufgabe \'{$a->edusignment}\' neu bearbeitet.';
$string['gradersubmissionupdatedtext'] = '{$a->username} hat die Aufgabe \'{$a->edusignment}\' bearbeitet und am {$a->timeupdated} hochgeladen.

Die Abgabe ist auf der Website verfügbar
{$a->url}';
$string['gradestudent'] = 'Bewertung für Teilnehmer/in: (id={$a->id}, Name={$a->fullname}).';
$string['gradeuser'] = 'Bewertung {$a}';
$string['grading'] = 'Unterschriften';
$string['gradingchangessaved'] = 'Die geänderten Unterschriften wurden gespeichert.';
$string['gradingmethodpreview'] = 'Bewertungskriterium';
$string['gradingoptions'] = 'Optionen';
$string['gradingstatus'] = 'Signaturstatus';
$string['gradingstudent'] = 'Signatur wird erwartet';
$string['gradingsummary'] = 'Signaturen - Überblick';
$string['grantextension'] = 'Verlängerung zulassen';
$string['grantextensionforusers'] = 'Verlängerung für {$a} Teillnehmer/innen zulassen';
$string['groupoverrides'] = 'Verfügbarkeitsänderung für Gruppen';
$string['groupoverridesdeleted'] = 'Verfügbarkeitsänderungen für Gruppen gelöscht';
$string['groupsnone'] = 'Keine Gruppe, auf die Sie zugreifen können.';
$string['groupsubmissionsettings'] = 'Einstellungen für Gruppeneinreichungen';
$string['hiddenuser'] = 'Teilnehmer/in';
$string['hideshow'] = 'Verbergen/Anzeigen';
$string['inactiveoverridehelp'] = '* Der Schüler hat nicht die korrekte Gruppe oder Rolle um die Aufgabe zu versuchen';
$string['indicator:cognitivedepth'] = 'Aufgabe kognitiv';
$string['indicator:cognitivedepth_help'] = 'Dieser Indikator basiert auf der kognitiven Tiefe, die ein/e Teilnehmer/in in einer Aufgabenaktivität erreicht hat.';
$string['indicator:socialbreadth'] = 'Aufgabe sozial';
$string['indicator:socialbreadth_help'] = 'Dieser Indikator basiert auf der sozialen Breite, die ein/e Teilnehmer/in in einer Aufgabenaktivität erreicht hat.';
$string['instructionfiles'] = 'Anleitungsdateien';
$string['introattachments'] = 'Zusätzliche Dateien';
$string['introattachments_help'] = 'Zusätzliche Dateien bei der Benutzung der Aktivität Aufgabe können hinzugefügt werden, z.B. Antwortvorlagen.';
$string['invalidfloatforgrade'] = 'Die eingegebene Bewertung \'{$a}\' scheint nicht korrekt zu sein. Bitte prüfen Sie die Eingabe.';
$string['invalidgradeforscale'] = 'Die eingegebene Bewertung ist bei der gewählten Bewertungsskala nicht vorgesehen.';
$string['invalidoverrideid'] = 'Ungültige Überschreibungs-ID';
$string['lastmodifiedgrade'] = 'Zuletzt geändert (Bewertung)';
$string['lastmodifiedsubmission'] = 'unterschrieben am';
$string['latesubmissions'] = 'Verspätete Abgaben';
$string['latesubmissionsaccepted'] = 'Erlaubt bis {$a}';
$string['loading'] = 'Laden...';
$string['locksubmissionforstudent'] = 'Weitere Abgaben verhindern von {$a->fullname} (id={$a->id})';
$string['locksubmissions'] = 'Abgabe sperren';
$string['manageedusignmentfeedbackplugins'] = 'Überblick über Feedback';
$string['manageedusignmentsubmissionplugins'] = 'Überblick über Abgabe';
$string['marker'] = 'Bewerter/in';
$string['markerfilter'] = 'Bewerter/in filtern';
$string['markerfilternomarker'] = 'Kein/e Bewerter/in';
$string['markingallocation'] = 'Bewerter-Zuordnung verwenden';
$string['markingallocation_help'] = 'Nach der Aktivierung können einzelnen Nutzer/innen Bewerter zugewiesen werden. Dazu muss der Bewertungsablaufstatus aktiviert worden sein.';
$string['markingworkflow'] = 'Bewertungsworkflow verwenden';
$string['markingworkflow_help'] = 'Nach der Aktivierung werden für Bewertungen mehrere Schritte durchlaufen bevor Teilnehmende sie sehen. Damit können mehrere Bewertungsdurchläufe erfolgen bevor alle Bewertungen zugleich den Teilnehmenden sichtbar gemacht werden.';
$string['markingworkflowstate'] = 'Status des Bewertungsworkflows';
$string['markingworkflowstate_help'] = 'Die Liste der Workflowstatus, die Sie auswählen können, wird durch die Berechtigungungen in der Aufgabe festgelegt. Es gibt folgende Status:

* Nicht bewertet - Der/die Bewerter/in hat noch nicht begonnen
* In Bewertung - Die Bewertung hat begonnen, ist jedoch noch nicht abgeschlossen
* Bewertung vorläufig abgeschlossen - Der Bewerter hat die Bewertung vorgenommen, jedoch noch nicht freigegeben
* In der Zweitbewertung - Die Bewertung wird nun von Zweitbewertern (Trainer) durchgesehen
* Fertig für Veröffentlichung - Der/die Trainer/in hat die abschließende Bewertung vorgenommen, wartet jedoch mit der Veröffentlichung
* Veröffentlicht - Teilnehmer/innen sehen ihre Bewertungen und das Feedback';
$string['markingworkflowstateinmarking'] = 'In Bewertung';
$string['markingworkflowstateinreview'] = 'In weiterer Überprüfung';
$string['markingworkflowstatenotmarked'] = 'Unbewertet';
$string['markingworkflowstatereadyforrelease'] = 'Fertig zur Freigabe';
$string['markingworkflowstatereadyforreview'] = 'Bewertung abgeschlossen';
$string['markingworkflowstatereleased'] = 'Freigegeben (Teilnehmer/innen informieren)';
$string['maxattempts'] = 'Maximal mögliche Versuche';
$string['maxattempts_help'] = 'Maximale Anzahl von Abgabeversuchen. Nach dieser Anzahl von Versuchen können Teilnehmer/innen ihre Abgabe nicht mehr neu öffnen oder ändern.';
$string['maxgrade'] = 'Bestwertung';
$string['maxperpage'] = 'Maximale Aufgaben pro Seite';
$string['maxperpage_help'] = 'Die Höchstzahl von Aufgaben, die ein Bewerter in der Bewertungsübersicht sehen kann. Nützlich zur Vermeidung von Timeout-Effekten bei Kursen mit sehr großen Nutzerzahlen.';
$string['messageprovider:edusignment_notification'] = 'Systemnachrichten zur Aufgabe';
$string['modulename'] = 'eduSign';
$string['modulename_help'] = 'Lassen Sie Dateien oder Text unterzeichnen.';
$string['modulenameplural'] = 'Aufgaben';
$string['moreusers'] = 'Weitere {$a}...';
$string['multipleteams'] = 'Mitglied in mehreren Gruppen';
$string['multipleteams_desc'] = 'Diese Aufgabe wird in Gruppen abgegeben. Sie sind Mitglied in mehr als einer Gruppe. Um die Aufgabe einzureichen müssen Sie Mitglied in genau einer Gruppe sein, damit Ihre Einreichung Ihrer Gruppe korrekt zugeordnet werden kann. Bitte kontaktieren Sie Ihren Trainer um Ihre Gruppenzugehörigkeit zu aktualisieren.';
$string['multipleteamsgrader'] = 'Mitglied in mehreren Gruppen. Die Abgabe von Aufgaben ist nicht möglich.';
$string['mysubmission'] = 'Meine Lösung:&nbsp;';
$string['newsubmissions'] = 'Aufgaben abgegeben';
$string['noattempt'] = 'Kein Versuch';
$string['noclose'] = 'Kein Enddatum';
$string['nofiles'] = 'Keine Dateien.';
$string['nofilters'] = 'Keine Filter';
$string['nograde'] = 'Keine Bewertung.';
$string['nolatesubmissions'] = 'Spätere Abgaben sind nicht zugelassen.';
$string['nomoresubmissionsaccepted'] = 'Weitere Abgaben sind nur zugelassen, wenn der Abgabezeitraum verlängert wurde.';
$string['none'] = 'Kein';
$string['noonlinesubmissions'] = 'Diese Aufgabe benötigt keine Online-Abgabe';
$string['noopen'] = 'Kein Startdatum';
$string['nooverridedata'] = 'Sie müssen mindestens eine Aufgabeneinstellung überschreiben.';
$string['nosavebutnext'] = 'Weiter';
$string['nosubmission'] = 'Für diese Aufgabe wurde nichts abgegeben';
$string['nosubmissionsacceptedafter'] = 'Weitere Abgaben sind nicht zugelassen nach';
$string['noteam'] = 'Nicht Mitglied einer Gruppe';
$string['noteam_desc'] = 'Diese Aufgabe wird in Gruppen abgegeben. Sie sind kein Mitglied einer Gruppe, deshalb können Sie die Aufgabe derzeit nicht einreichen. Bitte kontaktieren Sie Ihren Trainer um einer Gruppe hinzugefügt zu werden.';
$string['noteamgrader'] = 'Nicht Mitglied einer Gruppe. Die Abgabe von Aufgaben ist nicht möglich.';
$string['notgraded'] = 'Nicht kontrolliert';
$string['notgradedyet'] = 'Noch nicht kontrolliert';
$string['notifications'] = 'Systemnachrichten';
$string['notsubmittedyet'] = 'Noch nicht unterschrieben';
$string['nousers'] = 'Keine Nutzer/innen';
$string['nousersselected'] = 'Niemand ausgewählt';
$string['numberofdraftsubmissions'] = 'Entwürfe';
$string['numberofparticipants'] = 'Teilnehmer/innen';
$string['numberofsubmissionsneedgrading'] = 'Unterschriftenbestätigung erwartet';
$string['numberofsubmittededusignments'] = 'Abgegeben';
$string['numberofteams'] = 'Gruppen';
$string['offline'] = 'Keine Online-Abgabe notwendig';
$string['open'] = 'Offen';
$string['outlinegrade'] = 'Bewertung: {$a}';
$string['outof'] = '{$a->current} von {$a->total}';
$string['overdue'] = '<font color="red">Abgabeende überschritten seit: {$a}</font>';
$string['override'] = 'Überschreiben';
$string['overridedeletegroupsure'] = 'Möchten Sie wirklich die Überschreibung für die Gruppe {$a} löschen?';
$string['overridedeleteusersure'] = 'Möchten Sie wirklich die Überschreibung für Nutzer/in {$a} löschen?';
$string['overridegroup'] = 'Gruppe überschreiben';
$string['overridegroupeventname'] = '{$a->edusignment} - {$a->group}';
$string['overrides'] = 'Überschreibungen';
$string['overrideuser'] = 'Nutzer/in überschreiben';
$string['overrideusereventname'] = '{$a->edusignment} - Überschreibung';
$string['page-mod-edusignment-view'] = 'Aufgabenhauptseite';
$string['page-mod-edusignment-x'] = 'Jede Aufgabenseite';
$string['paramtimeremaining'] = 'Verbleibend: {$a}';
$string['participant'] = 'Teilnehmer/in';
$string['pluginadministration'] = 'Aufgaben-Administration';
$string['pluginname'] = 'eduSign';
$string['preventsubmissionnotingroup'] = 'Gruppe notwendig, um etwas abgeben zu können';
$string['preventsubmissionnotingroup_help'] = 'Diese Option legt fest, dass ausschließlich Mitglieder in Gruppen etwas abgeben können.';
$string['preventsubmissions'] = 'Verhindert die Abgabe von Lösungen für diese Aufgabe durch Teilnehmer/innen';
$string['preventsubmissionsshort'] = 'Abgabeänderung verhindern';
$string['previous'] = 'Zurück';
$string['privacy:attemptpath'] = 'Versuch {$a}';
$string['privacy:blindmarkingidentifier'] = 'Identifier, der für verdeckte Bewertungen genutzt wird';
$string['privacy:gradepath'] = 'Bewertung';
$string['privacy:metadata:edusignmentdownloadasfolders'] = 'Nutzerpräferenz, ob mehrere Dateien in einer  Aufgabenlösung in Ordnern sortiert heruntergeladen werden sollen.';
$string['privacy:metadata:edusignmentfeedbackpluginsummary'] = 'Feedbackeinträge für die Aufgabe';
$string['privacy:metadata:edusignmentfilter'] = 'Filteroptionen wie \'Eingereicht\', \'Nicht eingereicht\', \'Erfordert Bewertung\' und \'Verlängerung bewilligt\'.';
$string['privacy:metadata:edusignmentgrades'] = 'Speichert Bewertungen des Nutzers für die Aufgabe';
$string['privacy:metadata:edusignmentmarkerfilter'] = 'Aufgabenzusammenfassung gefiltert anhand des zugewiesenen Kennzeichens';
$string['privacy:metadata:edusignmentid'] = 'Aufgaben-ID';
$string['privacy:metadata:edusignmentmessageexplanation'] = 'Mitteilungen an den Nutzer über das Mitteilungssystem';
$string['privacy:metadata:edusignmentoverrides'] = 'Speichert überschriebene Informationen für die Aufgabe';
$string['privacy:metadata:edusignmentperpage'] = 'Anzahl der Aufgabenlösungen auf einer Seite';
$string['privacy:metadata:edusignmentquickgrading'] = 'Voreinstellung, ob die \'Schnelle Bewertung\' verwendet wird oder nicht';
$string['privacy:metadata:edusignmentsubmissiondetail'] = 'Speichert Informationen zur Lösung des Nutzers';
$string['privacy:metadata:edusignmentsubmissionpluginsummary'] = 'Lösungsdaten zur Aufgabe';
$string['privacy:metadata:edusignmentuserflags'] = 'Speichert Metadaten des Nutzers wie Verlängerungsdaten';
$string['privacy:metadata:edusignmentusermapping'] = 'Zuuodnung für verdeckte Bewertung';
$string['privacy:metadata:edusignmentworkflowfilter'] = 'Filter für verschiedene Workflow-Schritte';
$string['privacy:metadata:grade'] = 'Die Bewertung als Zahl für die Lösung der Aufgabe.  Kann von einer Skala oder mehreren Bewertungskriterien erzeugt sein. Es handelt sich immer um eine Zahl.';
$string['privacy:metadata:grader'] = 'Dies ist die Nutzer ID der bewertenden Person.';
$string['privacy:metadata:groupid'] = 'Dies ist die ID der Gruppe, zu der der Nutzer gehört.';
$string['privacy:metadata:latest'] = 'Stark vereinfachte Abfrage zum letzten Versuch';
$string['privacy:metadata:mailed'] = 'Wurde dem Nutzer bereits eine E-Mail geschickt?';
$string['privacy:metadata:timecreated'] = 'Erstelldatum';
$string['privacy:metadata:userid'] = 'Nutzer-ID';
$string['privacy:studentpath'] = 'Abgaben von Teilnehmern';
$string['privacy:submissionpath'] = 'Abgabe';
$string['quickgrading'] = 'Schnellbewertung';
$string['quickgradingchangessaved'] = 'Die Änderungen in der Bewertung wurden gespeichert';
$string['quickgrading_help'] = 'Die Schnellbewertung ermöglicht Ihnen direkt in der Übersichtstabelle Bewertungen vorzunehmen. Diese Möglichkeit steht nicht bei erweiterten Bewertungsmethoden zur Verfügung.';
$string['quickgradingresult'] = 'Schnellbewertung';
$string['recordid'] = 'ID';
$string['removeallgroupoverrides'] = 'Alle Verfügbarkeitsänderungen für Gruppen löschen';
$string['removealluseroverrides'] = 'Alle Verfügbarkeitsänderungen für Nutzer löschen';
$string['reopenuntilpassincompatiblewithblindmarking'] = 'Die Option \'Lösungen erneut bearbeitbar\'  ist mit der anonymen Bewertung nicht kompatibel, da die Bewertungen erst in den Bewertungsbereich übertragen werden, wenn die Identitäten der Teilnehmer/innen aufgedeckt werden.';
$string['requireallteammemberssubmit'] = 'Erfordert eine Abgabebestätigung durch alle Gruppenmitglieder';
$string['requireallteammemberssubmit_help'] = 'Wenn die Option aktiviert ist, müssen alle Gruppenmitglieder die eingereichte Lösung bestätigen, bevor eine Abgabe als abgegeben markiert wird.';
$string['requiresubmissionstatement'] = 'Erklärung zur Eigenständigkeit muss bestätigt werden';
$string['requiresubmissionstatement_help'] = 'Teilnehmer/innen müssen die Erklärung zur Eigenständigkeit bei Lösungen für diese Aufgabe abgeben.';
$string['revealidentities'] = 'Identität der Teilnehmenden aufdecken';
$string['revealidentitiesconfirm'] = 'Möchten Sie wirklich die Teilnehmeridentität für diese Aufgabe aufgedeckten? Die Einstellung kann nicht zurückgesetzt werden. Sobald die Teilnehmeridentität aufgedeckt ist, werden die Bewertungen in der Bewertungsübersicht angezeigt.';
$string['reverttodefaults'] = 'Aufgabeneinstellungen zurücksetzen';
$string['reverttodraft'] = 'Abgabe in den Entwurfsmodus zurücksetzen';
$string['reverttodraftforstudent'] = 'Den Status der Lösung auf Entwurf zurücksetzen für (id={$a->id}, fullname={$a->fullname}). Danach ist eine Bearbeitung wieder möglich.';
$string['reverttodraftshort'] = 'Abgabe in den Entwurfsmodus zurücksetzen';
$string['reviewed'] = 'Nachgeprüft';
$string['save'] = 'Speichern';
$string['saveallquickgradingchanges'] = 'Bewertungsänderungen sichern';
$string['saveandcontinue'] = 'Sichern und weiter';
$string['savechanges'] = 'Änderungen sichern';
$string['savegradingresult'] = 'Bewertung';
$string['savenext'] = 'Sichern und weiter';
$string['saveoverrideandstay'] = 'Speichern und weitere Überschreibung anlegen';
$string['savingchanges'] = 'Änderungen sichern...';
$string['scale'] = 'Skala';
$string['search:activity'] = 'Aufgabe - Beschreibung';
$string['selectedusers'] = 'Ausgewählte Nutzer/innen';
$string['selectlink'] = 'Auswählen...';
$string['selectuser'] = '{$a} auswählen';
$string['sendlatenotifications'] = 'Bewerter/innen über verspätete Abgaben von Lösungen informieren.';
$string['sendlatenotifications_help'] = 'Mit der Aktivierung werden die Bewerter (meist die Trainer/innen) benachrichtigt wenn eine Lösung verspätet abgegeben wird. Die Zustellung der Benachrichtigung ist individuell einstellbar.';
$string['sendnotifications'] = 'Mitteilungen an bewertende Personen senden';
$string['sendnotifications_help'] = 'Mit der Aktivierung werden die Bewerter (meist die Trainer/innen) benachrichtigt wenn eine Lösung zeitgerecht oder verspätet abgegeben wird. Die Zustellung der Benachrichtigung ist individuell einstellbar.';
$string['sendstudentnotifications'] = 'Teilnehmer/innen benachrichtigen';
$string['sendstudentnotificationsdefault'] = 'Standardeinstellung für Teilnehmer&shy;benachrichtigung';
$string['sendstudentnotificationsdefault_help'] = 'Den Standardwert für Auswahlfeld "Teilnehmer/innen benachrichtigen"  im Bewertungsformular festlegen.';
$string['sendstudentnotifications_help'] = 'Wenn aktiviert, dan erhalten Teilnehmer/innen eine Benachrichtigung über aktualisierte Bewertungen oder Feedbacks.';
$string['sendsubmissionreceipts'] = 'Abgabebestätigung an Teilnehmer/innen versenden';
$string['sendsubmissionreceipts_help'] = 'Diese Option aktiviert Bestätigungen, die bei der Abgabe von Aufgabenlösungen an die Teilnehmer/innen gesendet werden.';
$string['setmarkerallocationforlog'] = 'Bewertungszuordnung gesetzt auf: (id={$a->id}, Name={$a->fullname}, Bewerter={$a->marker}).';
$string['setmarkingallocation'] = 'Zugewiesene Bewerter/innen festlegen';
$string['setmarkingworkflowstate'] = 'Bewertungsworkflowstatus festlegen';
$string['setmarkingworkflowstateforlog'] = 'Bewertungsworkflow-Status gesetzt: (id={$a->id}, Name={$a->fullname}, Status={$a->state}).';
$string['settings'] = 'Einstellungen';
$string['showrecentsubmissions'] = 'Neue Abgaben anzeigen';
$string['signings'] = 'Unterschriften';
$string['status'] = 'Status';
$string['studentnotificationworkflowstateerror'] = 'Der Status für den Ablauf muss \'Freigegeben\' sein, um Teilnehmer/innen zu benachrichtigen.';
$string['submission'] = 'Abgabe';
$string['submissioncopiedhtml'] = '<p>Sie haben eine Kopie der früheren Lösung für die Aufgabe \'<i>{$a->edusignment} erstellt</i>\'</p><p>.
Sehen sehen hier den Status  <a href="{$a->url}"> für Ihre Aufgabenlösung</a>.</p>';
$string['submissioncopiedsmall'] = 'Sie haben Ihre bisherige Aufgabenlösung für {$a->edusignment} kopiert.';
$string['submissioncopiedtext'] = 'Sie haben Ihre bisherige Aufgabenlösung für {$a->edusignment} kopiert.

Sie können den Status der Aufgabenlösung sehen unter
    {$a->url}';
$string['submissiondrafts'] = 'Abgabetaste muss gedrückt werden';
$string['submissiondrafts_help'] = 'Diese Option ermöglicht es Teilnehmer/innen, die Lösung zu einer Aufgabe zunächst als Entwurf zu hinterlegen und sie später noch einmal zu überarbeiten. Erst mit der Bestätigung der Lösung als abgeschlossen werden die Trainer/innen aufgefordert, die Lösung zu bewerten.';
$string['submissioneditable'] = 'Teilnehmer/innen können eingereichte Lösung bearbeiten';
$string['submissionempty'] = 'Es wurde nichts eingereicht.';
$string['submissionlog'] = 'Teilnehmer/in: {$a->fullname}, Status: {$a->status}';
$string['submissionmodified'] = 'Sie haben bestehende Abgabedaten. Verlassen Sie die Seite und versuchen Sie es noch einmal.';
$string['submissionmodifiedgroup'] = 'Die Abgabe wurde von jemand anderem verändert. Verlassen Sie die Seite und versuchen Sie es noch einmal.';
$string['submissionnotcopiedinvalidstatus'] = 'Die Abgabe wurde nicht kopiert, weil sie seit dem Öffnen verändert wurde.';
$string['submissionnoteditable'] = 'Teilnehmer/innen können eingereichte Lösung nicht bearbeiten';
$string['submissionnotready'] = 'Diese Aufgabe ist nicht zur Abgabe fertig';
$string['submissionplugins'] = 'Plugins zur Abgabe';
$string['submissionreceipthtml'] = '<p>Sie haben eine Lösung zur Aufgabe \'<i>{$a->edusignment}</i>\' abgegeben.</p><p> Den Bewertungsstatus für die Aufgabe können Sie <a href="{$a->url}">hier</a> einsehen.</p>';
$string['submissionreceiptotherhtml'] = 'Ihre Aufgabenlösung für \'{$a->edusignment}\' wurde übermittelt.
<br /><br />
Sie können den Status <a href="{$a->url}">Ihrer Aufgabenlösung</a> sehen.';
$string['submissionreceiptothersmall'] = 'Ihre Aufgabenlösung für \'{$a->edusignment}\' wurde übermittelt.';
$string['submissionreceiptothertext'] = 'Ihre Aufgabenlösung für \'{$a->edusignment}\' wurde übermittelt.

Sie können den Status Ihrer Aufgabenlösung sehen unter {$a->url}';
$string['submissionreceipts'] = 'Abgabebestätigungen versenden';
$string['submissionreceiptsmall'] = 'Sie haben eine Lösung für {$a->edusignment} abgegeben.';
$string['submissionreceipttext'] = 'Sie haben eine Lösung für \'{$a->edusignment}\' abgegeben.

Sie können den Bewertungsstatus für die Aufgabe dort einsehen:

   {$a->url}';
$string['submissions'] = 'Abgegebene Aufgaben';
$string['submissionsclosed'] = 'Abgabe beendet';
$string['submissionsettings'] = 'Abgabeeinstellungen';
$string['submissionslocked'] = 'Bei dieser Aufgabe können derzeit keine Lösungen abgeben werden.';
$string['submissionslockedshort'] = 'Unterschriftänderung sind nicht erlaubt';
$string['submissionsnotgraded'] = 'Nicht bewertete Abgaben: {$a}';
$string['submissionstatement'] = 'Erklärung zur Eigenständigkeit';
$string['submissionstatementacceptedlog'] = 'Erklärung zur Eigenständigkeit wurde akzeptiert von {$a}';
$string['submissionstatementdefault'] = 'Diese Arbeit ist meine persönliche Leistung. Sofern ich irgendwo fremde Quellen verwendet habe, sind diese Stellen entsprechend gekennzeichnet.';
$string['submissionstatement_help'] = 'Erklärung zur Eigenständigkeit';
$string['submissionstatus'] = 'Unterschriftstatus';
$string['submissionstatus_'] = 'Keine Unterschrift';
$string['submissionstatus_draft'] = 'Entwurf (nicht abgegeben)';
$string['submissionstatusheading'] = 'Unterschriftstatus';
$string['submissionstatus_marked'] = 'Unterschrieben';
$string['submissionstatus_new'] = 'Keine Unterschrift';
$string['submissionstatus_reopened'] = 'Erneut geöffnet';
$string['submissionstatus_submitted'] = 'Unterschrieben';
$string['submissionsummary'] = '{$a->status}. Letzte Änderung {$a->timemodified}';
$string['submissionteam'] = 'Gruppe';
$string['submissiontypes'] = 'Abgabetypen';
$string['submitaction'] = 'Unterschreiben';
$string['submitedusignment'] = 'Unterschrift senden';
$string['submitedusignment_help'] = 'Sobald die Aufgabe abgegeben ist, können Sie keine Änderungen mehr vornehmen.';
$string['submitforgrading'] = 'Zur Überprüfung abgeben';
$string['submitted'] = 'Unterschrieben';
$string['submittedearly'] = 'Unterschriften wurde {$a} vor dem Abgabeende abgegeben';
$string['submittedlate'] = 'Unterschriften wurde {$a} verspätet abgegeben';
$string['submittedlateshort'] = '{$a} später';
$string['subplugintype_edusignmentfeedback'] = 'Feedback Plugin';
$string['subplugintype_edusignmentfeedback_plural'] = 'Feedback Plugins';
$string['subplugintype_edusignmentsubmission'] = 'Unterschriften Plugin';
$string['subplugintype_edusignmentsubmission_plural'] = 'Unterschriften Plugins';
$string['teamname'] = 'Team: {$a}';
$string['teamsubmission'] = 'Teilnehmer/innen geben in Gruppen ab';
$string['teamsubmissiongroupingid'] = 'Berücksichtigte Gruppierung';
$string['teamsubmissiongroupingid_help'] = 'Gruppen, die zu der gewählten Gruppierung gehören, werden zur Bearbeitung der Aufgabe genutzt. Wird keine Gruppierung ausgewählt, werden alle vorhandenen Gruppen verwendet.';
$string['teamsubmission_help'] = 'Mit der Aktivierung werden die Teilnehmer/innen in ihren Gruppen der Aufgabe zugeordnet. Eine Gruppenlösung steht allen Gruppenmitgliedern zur Verfügung. Änderungen können von allen eingesehen werden.';
$string['textinstructions'] = 'Aufgabenstellung';
$string['timemodified'] = 'Zuletzt geändert';
$string['timeremaining'] = 'Verbleibende Zeit';
$string['timeremainingcolon'] = 'Verbleibende Zeit: {$a}';
$string['togglezoom'] = 'Bereich zoomen';
$string['ungroupedusers'] = 'Die Option \'Gruppe notwendig, um etwas abgeben zu können\' ist aktiviert. Es gibt Personen ohne Gruppe oder Personen mit mehreren Gruppen, die deshalb nichts abgeben können.';
$string['unlimitedattempts'] = 'Unbegrenzt';
$string['unlimitedattemptsallowed'] = 'Unbegrenzte Versuche erlaubt';
$string['unlimitedpages'] = 'Unbegrenzt';
$string['unlocksubmissionforstudent'] = 'Abgabe für Teilnehmer/in erlauben: (id={$a->id}, Name={$a->fullname})';
$string['unlocksubmissions'] = 'Abgabe freigeben';
$string['unsavedchanges'] = 'Ungesicherte Änderungen';
$string['unsavedchangesquestion'] = 'Die Änderungen für Bewertung und Feedback sind noch nicht gesichert. Möchten Sie diese Änderungen sichern und fortfahren?';
$string['updategrade'] = 'Bewertung aktualisieren';
$string['updatetable'] = 'Sichern und Tabelle aktualisieren';
$string['upgradenotimplemented'] = 'Upgrade nicht implementiert für Plugin ({$a->type} {$a->subtype})';
$string['userextensiondate'] = 'Verlängertes Abgabeende bis: {$a}';
$string['usergrade'] = 'Nutzerbewertung';
$string['useridlistnotcached'] = 'Abbruch des Speichervorgangs. Moodle konnte nicht erkennen für welchen Nutzer die Bewertung gespeichert werden soll.';
$string['useroverrides'] = 'Verfügbarkeitsänderung für Nutzer';
$string['useroverridesdeleted'] = 'Verfügbarkeitsänderungen für Nutzer gelöscht';
$string['usersnone'] = 'Kein/e Teilnehmer/in hat Zugriff auf diese Aufgabe.';
$string['userswhoneedtosubmit'] = 'Nutzer/innen, die noch nicht abgegeben haben: {$a}';
$string['validmarkingworkflowstates'] = 'Gültige Status für Bewertungsworkflow';
$string['viewadifferentattempt'] = 'Anderen Versuch anzeigen';
$string['viewbatchmarkingallocation'] = 'Stapelverarbeitung für Bewerterzuordnung anzeigen.';
$string['viewbatchsetmarkingworkflowstate'] = 'Stapelverarbeitung für Bewertungsworkflow angesehen';
$string['viewfeedback'] = 'Feedback anzeigen';
$string['viewfeedbackforuser'] = 'Feedback anzeigen für: {$a}';
$string['viewfull'] = 'Vollständige Anzeige';
$string['viewfullgradingpage'] = 'Die komplette Unterschriftenübersichtsseite öffnen, um ein Feedback zu erstellen.';
$string['viewgradebook'] = 'Bewertungen anzeigen';
$string['viewgrading'] = 'Alle Unterschriften anzeigen';
$string['viewgradingformforstudent'] = 'Unterschriftenseite für Teilnehmer/in: (id={$a->id}, fullname={$a->fullname}) anzeigen.';
$string['viewownsubmissionform'] = 'Seite mit meinen eigenen Lösungen für Aufgaben anzeigen.';
$string['viewownsubmissionstatus'] = 'Eigene Statusseite zur Abgabe anzeigen';
$string['viewrevealidentitiesconfirm'] = 'Bestätigungsseite mit Teilnehmernamen anzeigen';
$string['viewsubmission'] = 'Unterschrift anzeigen';
$string['viewsubmissionforuser'] = 'Abgabe von {$a} anzeigen';
$string['viewsubmissiongradingtable'] = 'Unterschriften zur Abgabe anzeigen';
$string['viewsummary'] = 'Zusammenfassung anzeigen';
$string['workflowfilter'] = 'Workflow-Filter';
$string['xofy'] = '{$a->x} von {$a->y}';
