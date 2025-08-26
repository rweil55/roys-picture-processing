<?php
class fixTaskList
{
  public static function showlist($attr)
  {
    global $eol, $errorBeg, $errorEnd;
    $msg = "";
    $msg .= "
       <a href='/fix/?task=add' >upload photos from the source table</a>$eol
       <a href='/fix/?task=addlist' >list available photos from the source
                                        table</a>$eol
       <a href='/fix/?task=by date' > list photos aded between 'startdate' and
                                        'enddate' </a>$eol
       <a href='/admin#delete' > delete photo - See admin </a>$eol
       <a href='/fix/?task=duplicatephoto' > set duplicate photo flag </a>$eol
       <a href='/fix/?task=exifmissing' > photo with no exif data </a>$eol
       <a href='/fix/?task=forcedatabase' > force database to match exif </a>$eol
       <a href='/fix/?task=rename' > rename a photo </a>$eol
<strong>File consistancy</strong>$eol
      <a href='/fix/?task=highresmissing' > high resolution image
                                            missing </a>$eol
      <a href='/fix/?task=photomissing' > photo information missing</a>$eol
      <a href='/fix/?task=filesmissing' > highres missng _cr, _tmb </a>$eol
      <a href='/fix/?task=compareexif' > compare highres and _cr exif data </a>$eol

   <strong>Copyright</strong>$eol
     <a href='/fix/?task=badcopyright' > copyright not begining with
                                        copyright </a>$eol
       <a href='/fix/?task=copyrightfix' > fix missing Copyright based on
                                        photographer </a>$eol
    <strong>souce data</strong>$eol
        <a href='/fix/?task=SourceLocClose' > close match photoname of
                                            dire/photoname spoke</a>$eol
       <a href='/fix/?task=SourceLocMatch' > close match photoname of
                                            dire/photoname spoke </a>$eol
      <a href='/fix/?task=filelike' > find source file like 'partfile' </a>$eol
   <strong>keywords</strong>$eol
      <a href='/fix/?task=keywordDups' > list photos with
                                            duplicate keywords </a>$eol
       <a href='/fix/?task=extrakeyword' > list sql to update keyword table
                                                from photo descrioin</a>$eol
        <a href='/fix/?task=keywordForm' > merge/delete keywords </a>$eol

      ";
    return $msg;
  }
} //end class
