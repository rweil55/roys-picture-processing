<?php

use lsolesen\pel\Pel;
use lsolesen\pel\PelConvert;
use lsolesen\pel\PelCanonMakerNotes;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelEntryException;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryCopyright;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelEntryNumber;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelEntrySShort;
use lsolesen\pel\PelEntrySRational;
use lsolesen\pel\PelEntrySLong;
use lsolesen\pel\PelEntryTime;
//use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelEntryUserCopyright;
use lsolesen\pel\PelEntryVersion;
use lsolesen\pel\PelEntryWindowsString;
use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelFormat;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelIfdException;
use lsolesen\pel\PelIllegalFormatException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelJpegComment;
use lsolesen\pel\PelJpegContent;
use lsolesen\pel\PelJpegInvalidMarkerException;
use lsolesen\pel\PelJpegMarker;
use lsolesen\pel\PelMakerNotes;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelWrongComponentCountException;
$pel = "pel-master/src";
require_once "$pel/Pel.php";
require_once "$pel/PelException.php";
require_once "$pel/PelInvalidArgumentException.php";
require_once "$pel/PelJpegInvalidMarkerException.php";
require_once "$pel/PelJpegContent.php";
require_once "$pel/PelMakerNotes.php";
require_once "$pel/PelCanonMakerNotes.php";
require_once "$pel/PelConvert.php";
require_once "$pel/PelDataWindow.php";
require_once "$pel/PelEntry.php";
require_once "$pel/PelEntryException.php";
require_once "$pel/PelEntryNumber.php";
require_once "$pel/PelEntryAscii.php";
require_once "$pel/PelEntryByte.php";
require_once "$pel/PelEntryCopyright.php";
require_once "$pel/PelEntryLong.php";
require_once "$pel/PelEntryRational.php";
require_once "$pel/PelEntrySLong.php";
require_once "$pel/PelEntrySRational.php";
require_once "$pel/PelEntryShort.php";
require_once "$pel/PelEntrySShort.php";
require_once "$pel/PelEntryTime.php";
require_once "$pel/PelEntryShort.php";
require_once "$pel/PelEntryUndefined.php";
require_once "$pel/PelEntryUserComment.php";
require_once "$pel/PelEntryVersion.php";
require_once "$pel/PelEntryWindowsString.php";
require_once "$pel/PelExif.php";
require_once "$pel/PelFormat.php";
require_once "$pel/PelIfd.php";
require_once "$pel/PelIfdException.php";
require_once "$pel/PelIllegalFormatException.php";
require_once "$pel/PelInvalidDataException.php";
require_once "$pel/PelJpeg.php";
require_once "$pel/PelJpegComment.php";
require_once "$pel/PelJpegMarker.php";
require_once "$pel/PelTag.php";
require_once "$pel/PelTiff.php";
require_once "$pel/PelWrongComponentCountException.php";
?>