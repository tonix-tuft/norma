<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"], to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Norma\HTTP\MIME;

/**
 * A non-instantiable class which contains useful constants to work with MIME types.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
final class MIMETypes {

    /**
     * Maps file extensions to their corresponding MIME type string.
     * 
     * @see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     * 
     * @var string[]|array[]
     */
    const FILE_EXTENSION_MIME_TYPE_MAP = [
        '123' => 'application/vnd.lotus-1-2-3',
        '3dml' => 'text/vnd.in3d.3dml',
        '3ds' => 'image/x-3ds',
        '3g2' => 'video/3gpp2',
        '3gp' => 'video/3gpp',
        '7z' => 'application/x-7z-compressed',
        'aab' => 'application/x-authorware-bin',
        'aac' => 'audio/x-aac',
        'aam' => 'application/x-authorware-map',
        'aas' => 'application/x-authorware-seg',
        'abw' => 'application/x-abiword',
        'ac' => 'application/pkix-attr-cert',
        'acc' => 'application/vnd.americandynamics.acc',
        'ace' => 'application/x-ace-compressed',
        'acu' => 'application/vnd.acucobol',
        'acutc' => 'application/vnd.acucorp',
        'adp' => 'audio/adpcm',
        'aep' => 'application/vnd.audiograph',
        'afm' => 'application/x-font-type1',
        'afp' => 'application/vnd.ibm.modcap',
        'ahead' => 'application/vnd.ahead.space',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'air' => 'application/vnd.adobe.air-application-installer-package+zip',
        'ait' => 'application/vnd.dvb.ait',
        'ami' => 'application/vnd.amiga.ami',
        'apk' => 'application/vnd.android.package-archive',
        'appcache' => 'text/cache-manifest',
        'application' => 'application/x-ms-application',
        'apr' => 'application/vnd.lotus-approach',
        'arc' => 'application/x-freearc',
        'asc' => 'application/pgp-signature',
        'asf' => 'video/x-ms-asf',
        'asm' => 'text/x-asm',
        'aso' => 'application/vnd.accpac.simply.aso',
        'asx' => 'video/x-ms-asf',
        'atc' => 'application/vnd.acucorp',
        'atom' => 'application/atom+xml',
        'atomcat' => 'application/atomcat+xml',
        'atomsvc' => 'application/atomsvc+xml',
        'atx' => 'application/vnd.antix.game-component',
        'au' => 'audio/basic',
        'avi' => [
            'video/x-msvideo',
            'video/avi',
        ],
        'aw' => 'application/applixware',
        'azf' => 'application/vnd.airzip.filesecure.azf',
        'azs' => 'application/vnd.airzip.filesecure.azs',
        'azw' => 'application/vnd.amazon.ebook',
        'bat' => 'application/x-msdownload',
        'bcpio' => 'application/x-bcpio',
        'bdf' => 'application/x-font-bdf',
        'bdm' => 'application/vnd.syncml.dm+wbxml',
        'bed' => 'application/vnd.realvnc.bed',
        'bh2' => 'application/vnd.fujitsu.oasysprs',
        'bin' => 'application/octet-stream',
        'blb' => 'application/x-blorb',
        'blorb' => 'application/x-blorb',
        'bmi' => 'application/vnd.bmi',
        'bmp' => 'image/bmp',
        'book' => 'application/vnd.framemaker',
        'box' => 'application/vnd.previewsystems.box',
        'boz' => 'application/x-bzip2',
        'bpk' => 'application/octet-stream',
        'btif' => 'image/prs.btif',
        'bz' => 'application/x-bzip',
        'bz2' => 'application/x-bzip2',
        'c' => 'text/x-c',
        'c11amc' => 'application/vnd.cluetrust.cartomobile-config',
        'c11amz' => 'application/vnd.cluetrust.cartomobile-config-pkg',
        'c4d' => 'application/vnd.clonk.c4group',
        'c4f' => 'application/vnd.clonk.c4group',
        'c4g' => 'application/vnd.clonk.c4group',
        'c4p' => 'application/vnd.clonk.c4group',
        'c4u' => 'application/vnd.clonk.c4group',
        'cab' => 'application/vnd.ms-cab-compressed',
        'caf' => 'audio/x-caf',
        'cap' => 'application/vnd.tcpdump.pcap',
        'car' => 'application/vnd.curl.car',
        'cat' => 'application/vnd.ms-pki.seccat',
        'cb7' => 'application/x-cbr',
        'cba' => 'application/x-cbr',
        'cbr' => 'application/x-cbr',
        'cbt' => 'application/x-cbr',
        'cbz' => 'application/x-cbr',
        'cc' => 'text/x-c',
        'cct' => 'application/x-director',
        'ccxml' => 'application/ccxml+xml',
        'cdbcmsg' => 'application/vnd.contact.cmsg',
        'cdf' => 'application/x-netcdf',
        'cdkey' => 'application/vnd.mediastation.cdkey',
        'cdmia' => 'application/cdmi-capability',
        'cdmic' => 'application/cdmi-container',
        'cdmid' => 'application/cdmi-domain',
        'cdmio' => 'application/cdmi-object',
        'cdmiq' => 'application/cdmi-queue',
        'cdx' => 'chemical/x-cdx',
        'cdxml' => 'application/vnd.chemdraw+xml',
        'cdy' => 'application/vnd.cinderella',
        'cer' => 'application/pkix-cert',
        'cfs' => 'application/x-cfs-compressed',
        'cgm' => 'image/cgm',
        'chat' => 'application/x-chat',
        'chm' => 'application/vnd.ms-htmlhelp',
        'chrt' => 'application/vnd.kde.kchart',
        'cif' => 'chemical/x-cif',
        'cii' => 'application/vnd.anser-web-certificate-issue-initiation',
        'cil' => 'application/vnd.ms-artgalry',
        'cla' => 'application/vnd.claymore',
        'class' => 'application/java-vm',
        'clkk' => 'application/vnd.crick.clicker.keyboard',
        'clkp' => 'application/vnd.crick.clicker.palette',
        'clkt' => 'application/vnd.crick.clicker.template',
        'clkw' => 'application/vnd.crick.clicker.wordbank',
        'clkx' => 'application/vnd.crick.clicker',
        'clp' => 'application/x-msclip',
        'cmc' => 'application/vnd.cosmocaller',
        'cmdf' => 'chemical/x-cmdf',
        'cml' => 'chemical/x-cml',
        'cmp' => 'application/vnd.yellowriver-custom-menu',
        'cmx' => 'image/x-cmx',
        'cod' => 'application/vnd.rim.cod',
        'com' => 'application/x-msdownload',
        'conf' => 'text/plain',
        'cpio' => 'application/x-cpio',
        'cpp' => 'text/x-c',
        'cpt' => 'application/mac-compactpro',
        'crd' => 'application/x-mscardfile',
        'crl' => 'application/pkix-crl',
        'crt' => 'application/x-x509-ca-cert',
        'cryptonote' => 'application/vnd.rig.cryptonote',
        'csh' => 'application/x-csh',
        'csml' => 'chemical/x-csml',
        'csp' => 'application/vnd.commonspace',
        'css' => 'text/css',
        'cst' => 'application/x-director',
        'csv' => 'text/csv',
        'cu' => 'application/cu-seeme',
        'curl' => 'text/vnd.curl',
        'cww' => 'application/prs.cww',
        'cxt' => 'application/x-director',
        'cxx' => 'text/x-c',
        'dae' => 'model/vnd.collada+xml',
        'daf' => 'application/vnd.mobius.daf',
        'dart' => 'application/vnd.dart',
        'dataless' => 'application/vnd.fdsn.seed',
        'davmount' => 'application/davmount+xml',
        'dbk' => 'application/docbook+xml',
        'dcr' => 'application/x-director',
        'dcurl' => 'text/vnd.curl.dcurl',
        'dd2' => 'application/vnd.oma.dd2+xml',
        'ddd' => 'application/vnd.fujixerox.ddd',
        'deb' => 'application/x-debian-package',
        'def' => 'text/plain',
        'deploy' => 'application/octet-stream',
        'der' => 'application/x-x509-ca-cert',
        'dfac' => 'application/vnd.dreamfactory',
        'dgc' => 'application/x-dgc-compressed',
        'dic' => 'text/x-c',
        'dir' => 'application/x-director',
        'dis' => 'application/vnd.mobius.dis',
        'dist' => 'application/octet-stream',
        'distz' => 'application/octet-stream',
        'djv' => 'image/vnd.djvu',
        'djvu' => 'image/vnd.djvu',
        'dll' => 'application/x-msdownload',
        'dmg' => 'application/x-apple-diskimage',
        'dmp' => 'application/vnd.tcpdump.pcap',
        'dms' => 'application/octet-stream',
        'dna' => 'application/vnd.dna',
        'doc' => 'application/msword',
        'docm' => 'application/vnd.ms-word.document.macroenabled.12',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dot' => 'application/msword',
        'dotm' => 'application/vnd.ms-word.template.macroenabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dp' => 'application/vnd.osgi.dp',
        'dpg' => 'application/vnd.dpgraph',
        'dra' => 'audio/vnd.dra',
        'dsc' => 'text/prs.lines.tag',
        'dssc' => 'application/dssc+der',
        'dtb' => 'application/x-dtbook+xml',
        'dtd' => 'application/xml-dtd',
        'dts' => 'audio/vnd.dts',
        'dtshd' => 'audio/vnd.dts.hd',
        'dump' => 'application/octet-stream',
        'dvb' => 'video/vnd.dvb.file',
        'dvi' => 'application/x-dvi',
        'dwf' => 'model/vnd.dwf',
        'dwg' => 'image/vnd.dwg',
        'dxf' => 'image/vnd.dxf',
        'dxp' => 'application/vnd.spotfire.dxp',
        'dxr' => 'application/x-director',
        'ecelp4800' => 'audio/vnd.nuera.ecelp4800',
        'ecelp7470' => 'audio/vnd.nuera.ecelp7470',
        'ecelp9600' => 'audio/vnd.nuera.ecelp9600',
        'ecma' => 'application/ecmascript',
        'edm' => 'application/vnd.novadigm.edm',
        'edx' => 'application/vnd.novadigm.edx',
        'efif' => 'application/vnd.picsel',
        'ei6' => 'application/vnd.pg.osasli',
        'elc' => 'application/octet-stream',
        'emf' => 'application/x-msmetafile',
        'eml' => 'message/rfc822',
        'emma' => 'application/emma+xml',
        'emz' => 'application/x-msmetafile',
        'eol' => 'audio/vnd.digital-winds',
        'eot' => 'application/vnd.ms-fontobject',
        'eps' => 'application/postscript',
        'epub' => 'application/epub+zip',
        'es3' => 'application/vnd.eszigno3+xml',
        'esa' => 'application/vnd.osgi.subsystem',
        'esf' => 'application/vnd.epson.esf',
        'et3' => 'application/vnd.eszigno3+xml',
        'etx' => 'text/x-setext',
        'eva' => 'application/x-eva',
        'evy' => 'application/x-envoy',
        'exe' => 'application/x-msdownload',
        'exi' => 'application/exi',
        'ext' => 'application/vnd.novadigm.ext',
        'ez' => 'application/andrew-inset',
        'ez2' => 'application/vnd.ezpix-album',
        'ez3' => 'application/vnd.ezpix-package',
        'f' => 'text/x-fortran',
        'f4v' => 'video/x-f4v',
        'f77' => 'text/x-fortran',
        'f90' => 'text/x-fortran',
        'fbs' => 'image/vnd.fastbidsheet',
        'fcdt' => 'application/vnd.adobe.formscentral.fcdt',
        'fcs' => 'application/vnd.isac.fcs',
        'fdf' => 'application/vnd.fdf',
        'fe_launch' => 'application/vnd.denovo.fcselayout-link',
        'fg5' => 'application/vnd.fujitsu.oasysgp',
        'fgd' => 'application/x-director',
        'fh' => 'image/x-freehand',
        'fh4' => 'image/x-freehand',
        'fh5' => 'image/x-freehand',
        'fh7' => 'image/x-freehand',
        'fhc' => 'image/x-freehand',
        'fig' => 'application/x-xfig',
        'flac' => 'audio/x-flac',
        'fli' => 'video/x-fli',
        'flo' => 'application/vnd.micrografx.flo',
        'flv' => 'video/x-flv',
        'flw' => 'application/vnd.kde.kivio',
        'flx' => 'text/vnd.fmi.flexstor',
        'fly' => 'text/vnd.fly',
        'fm' => 'application/vnd.framemaker',
        'fnc' => 'application/vnd.frogans.fnc',
        'for' => 'text/x-fortran',
        'fpx' => 'image/vnd.fpx',
        'frame' => 'application/vnd.framemaker',
        'fsc' => 'application/vnd.fsc.weblaunch',
        'fst' => 'image/vnd.fst',
        'ftc' => 'application/vnd.fluxtime.clip',
        'fti' => 'application/vnd.anser-web-funds-transfer-initiation',
        'fvt' => 'video/vnd.fvt',
        'fxp' => 'application/vnd.adobe.fxp',
        'fxpl' => 'application/vnd.adobe.fxp',
        'fzs' => 'application/vnd.fuzzysheet',
        'g2w' => 'application/vnd.geoplan',
        'g3' => 'image/g3fax',
        'g3w' => 'application/vnd.geospace',
        'gac' => 'application/vnd.groove-account',
        'gam' => 'application/x-tads',
        'gbr' => 'application/rpki-ghostbusters',
        'gca' => 'application/x-gca-compressed',
        'gdl' => 'model/vnd.gdl',
        'geo' => 'application/vnd.dynageo',
        'gex' => 'application/vnd.geometry-explorer',
        'ggb' => 'application/vnd.geogebra.file',
        'ggt' => 'application/vnd.geogebra.tool',
        'ghf' => 'application/vnd.groove-help',
        'gif' => 'image/gif',
        'gim' => 'application/vnd.groove-identity-message',
        'gml' => 'application/gml+xml',
        'gmx' => 'application/vnd.gmx',
        'gnumeric' => 'application/x-gnumeric',
        'gph' => 'application/vnd.flographit',
        'gpx' => 'application/gpx+xml',
        'gqf' => 'application/vnd.grafeq',
        'gqs' => 'application/vnd.grafeq',
        'gram' => 'application/srgs',
        'gramps' => 'application/x-gramps-xml',
        'gre' => 'application/vnd.geometry-explorer',
        'grv' => 'application/vnd.groove-injector',
        'grxml' => 'application/srgs+xml',
        'gsf' => 'application/x-font-ghostscript',
        'gtar' => 'application/x-gtar',
        'gtm' => 'application/vnd.groove-tool-message',
        'gtw' => 'model/vnd.gtw',
        'gv' => 'text/vnd.graphviz',
        'gxf' => 'application/gxf',
        'gxt' => 'application/vnd.geonext',
        'h' => 'text/x-c',
        'h261' => 'video/h261',
        'h263' => 'video/h263',
        'h264' => 'video/h264',
        'hal' => 'application/vnd.hal+xml',
        'hbci' => 'application/vnd.hbci',
        'hdf' => 'application/x-hdf',
        'hh' => 'text/x-c',
        'hlp' => 'application/winhlp',
        'hpgl' => 'application/vnd.hp-hpgl',
        'hpid' => 'application/vnd.hp-hpid',
        'hps' => 'application/vnd.hp-hps',
        'hqx' => 'application/mac-binhex40',
        'htke' => 'application/vnd.kenameaapp',
        'htm' => 'text/html',
        'html' => 'text/html',
        'hvd' => 'application/vnd.yamaha.hv-dic',
        'hvp' => 'application/vnd.yamaha.hv-voice',
        'hvs' => 'application/vnd.yamaha.hv-script',
        'i2g' => 'application/vnd.intergeo',
        'icc' => 'application/vnd.iccprofile',
        'ice' => 'x-conference/x-cooltalk',
        'icm' => 'application/vnd.iccprofile',
        'ico' => 'image/x-icon',
        'ics' => 'text/calendar',
        'ief' => 'image/ief',
        'ifb' => 'text/calendar',
        'ifm' => 'application/vnd.shana.informed.formdata',
        'iges' => 'model/iges',
        'igl' => 'application/vnd.igloader',
        'igm' => 'application/vnd.insors.igm',
        'igs' => 'model/iges',
        'igx' => 'application/vnd.micrografx.igx',
        'iif' => 'application/vnd.shana.informed.interchange',
        'imp' => 'application/vnd.accpac.simply.imp',
        'ims' => 'application/vnd.ms-ims',
        'in' => 'text/plain',
        'ink' => 'application/inkml+xml',
        'inkml' => 'application/inkml+xml',
        'install' => 'application/x-install-instructions',
        'iota' => 'application/vnd.astraea-software.iota',
        'ipfix' => 'application/ipfix',
        'ipk' => 'application/vnd.shana.informed.package',
        'irm' => 'application/vnd.ibm.rights-management',
        'irp' => 'application/vnd.irepository.package+xml',
        'iso' => 'application/x-iso9660-image',
        'itp' => 'application/vnd.shana.informed.formtemplate',
        'ivp' => 'application/vnd.immervision-ivp',
        'ivu' => 'application/vnd.immervision-ivu',
        'jad' => 'text/vnd.sun.j2me.app-descriptor',
        'jam' => 'application/vnd.jam',
        'jar' => 'application/java-archive',
        'java' => 'text/x-java-source',
        'jisp' => 'application/vnd.jisp',
        'jlt' => 'application/vnd.hp-jlyt',
        'jnlp' => 'application/x-java-jnlp-file',
        'joda' => 'application/vnd.joost.joda-archive',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpgm' => 'video/jpm',
        'jpgv' => 'video/jpeg',
        'jpm' => 'video/jpm',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'jsonml' => 'application/jsonml+json',
        'kar' => 'audio/midi',
        'karbon' => 'application/vnd.kde.karbon',
        'key' => 'application/x-iwork-keynote-sffkey',
        'kfo' => 'application/vnd.kde.kformula',
        'kia' => 'application/vnd.kidspiration',
        'kml' => 'application/vnd.google-earth.kml+xml',
        'kmz' => 'application/vnd.google-earth.kmz',
        'kne' => 'application/vnd.kinar',
        'knp' => 'application/vnd.kinar',
        'kon' => 'application/vnd.kde.kontour',
        'kpr' => 'application/vnd.kde.kpresenter',
        'kpt' => 'application/vnd.kde.kpresenter',
        'kpxx' => 'application/vnd.ds-keypoint',
        'ksp' => 'application/vnd.kde.kspread',
        'ktr' => 'application/vnd.kahootz',
        'ktx' => 'image/ktx',
        'ktz' => 'application/vnd.kahootz',
        'kwd' => 'application/vnd.kde.kword',
        'kwt' => 'application/vnd.kde.kword',
        'lasxml' => 'application/vnd.las.las+xml',
        'latex' => 'application/x-latex',
        'lbd' => 'application/vnd.llamagraphics.life-balance.desktop',
        'lbe' => 'application/vnd.llamagraphics.life-balance.exchange+xml',
        'les' => 'application/vnd.hhe.lesson-player',
        'lha' => 'application/x-lzh-compressed',
        'link66' => 'application/vnd.route66.link66+xml',
        'list' => 'text/plain',
        'list3820' => 'application/vnd.ibm.modcap',
        'listafp' => 'application/vnd.ibm.modcap',
        'lnk' => 'application/x-ms-shortcut',
        'log' => 'text/plain',
        'lostxml' => 'application/lost+xml',
        'lrf' => 'application/octet-stream',
        'lrm' => 'application/vnd.ms-lrm',
        'ltf' => 'application/vnd.frogans.ltf',
        'lvp' => 'audio/vnd.lucent.voice',
        'lwp' => 'application/vnd.lotus-wordpro',
        'lzh' => 'application/x-lzh-compressed',
        'm13' => 'application/x-msmediaview',
        'm14' => 'application/x-msmediaview',
        'm1v' => 'video/mpeg',
        'm21' => 'application/mp21',
        'm2a' => 'audio/mpeg',
        'm2v' => 'video/mpeg',
        'm3a' => 'audio/mpeg',
        'm3u' => 'audio/x-mpegurl',
        'm3u8' => 'application/vnd.apple.mpegurl',
        'm4a' => 'audio/mp4',
        'm4u' => 'video/vnd.mpegurl',
        'm4v' => 'video/x-m4v',
        'ma' => 'application/mathematica',
        'mads' => 'application/mads+xml',
        'mag' => 'application/vnd.ecowin.chart',
        'maker' => 'application/vnd.framemaker',
        'man' => 'text/troff',
        'mar' => 'application/octet-stream',
        'mathml' => 'application/mathml+xml',
        'mb' => 'application/mathematica',
        'mbk' => 'application/vnd.mobius.mbk',
        'mbox' => 'application/mbox',
        'mc1' => 'application/vnd.medcalcdata',
        'mcd' => 'application/vnd.mcd',
        'mcurl' => 'text/vnd.curl.mcurl',
        'mdb' => 'application/x-msaccess',
        'mdi' => 'image/vnd.ms-modi',
        'me' => 'text/troff',
        'mesh' => 'model/mesh',
        'meta4' => 'application/metalink4+xml',
        'metalink' => 'application/metalink+xml',
        'mets' => 'application/mets+xml',
        'mfm' => 'application/vnd.mfmp',
        'mft' => 'application/rpki-manifest',
        'mgp' => 'application/vnd.osgeo.mapguide.package',
        'mgz' => 'application/vnd.proteus.magazine',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mie' => 'application/x-mie',
        'mif' => 'application/vnd.mif',
        'mime' => 'message/rfc822',
        'mj2' => 'video/mj2',
        'mjp2' => 'video/mj2',
        'mk3d' => 'video/x-matroska',
        'mka' => 'audio/x-matroska',
        'mks' => 'video/x-matroska',
        'mkv' => 'video/x-matroska',
        'mlp' => 'application/vnd.dolby.mlp',
        'mmd' => 'application/vnd.chipnuts.karaoke-mmd',
        'mmf' => 'application/vnd.smaf',
        'mmr' => 'image/vnd.fujixerox.edmics-mmr',
        'mng' => 'video/x-mng',
        'mny' => 'application/x-msmoney',
        'mobi' => 'application/x-mobipocket-ebook',
        'mods' => 'application/mods+xml',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'audio/mpeg',
        'mp21' => 'application/mp21',
        'mp2a' => 'audio/mpeg',
        'mp3' => [
            'audio/mp3',
            'audio/mpeg',
        ],
        'mp4' => 'video/mp4',
        'mp4a' => 'audio/mp4',
        'mp4s' => 'application/mp4',
        'mp4v' => 'video/mp4',
        'mpc' => 'application/vnd.mophun.certificate',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpg4' => 'video/mp4',
        'mpga' => 'audio/mpeg',
        'mpkg' => 'application/vnd.apple.installer+xml',
        'mpm' => 'application/vnd.blueice.multipass',
        'mpn' => 'application/vnd.mophun.application',
        'mpp' => 'application/vnd.ms-project',
        'mpt' => 'application/vnd.ms-project',
        'mpy' => 'application/vnd.ibm.minipay',
        'mqy' => 'application/vnd.mobius.mqy',
        'mrc' => 'application/marc',
        'mrcx' => 'application/marcxml+xml',
        'ms' => 'text/troff',
        'mscml' => 'application/mediaservercontrol+xml',
        'mseed' => 'application/vnd.fdsn.mseed',
        'mseq' => 'application/vnd.mseq',
        'msf' => 'application/vnd.epson.msf',
        'msh' => 'model/mesh',
        'msi' => 'application/x-msdownload',
        'msl' => 'application/vnd.mobius.msl',
        'msty' => 'application/vnd.muvee.style',
        'mts' => 'model/vnd.mts',
        'mus' => 'application/vnd.musician',
        'musicxml' => 'application/vnd.recordare.musicxml+xml',
        'mvb' => 'application/x-msmediaview',
        'mwf' => 'application/vnd.mfer',
        'mxf' => 'application/mxf',
        'mxl' => 'application/vnd.recordare.musicxml',
        'mxml' => 'application/xv+xml',
        'mxs' => 'application/vnd.triscape.mxs',
        'mxu' => 'video/vnd.mpegurl',
        'n-gage' => 'application/vnd.nokia.n-gage.symbian.install',
        'n3' => 'text/n3',
        'nb' => 'application/mathematica',
        'nbp' => 'application/vnd.wolfram.player',
        'nc' => 'application/x-netcdf',
        'ncx' => 'application/x-dtbncx+xml',
        'nfo' => 'text/x-nfo',
        'ngdat' => 'application/vnd.nokia.n-gage.data',
        'nitf' => 'application/vnd.nitf',
        'nlu' => 'application/vnd.neurolanguage.nlu',
        'nml' => 'application/vnd.enliven',
        'nnd' => 'application/vnd.noblenet-directory',
        'nns' => 'application/vnd.noblenet-sealer',
        'nnw' => 'application/vnd.noblenet-web',
        'npx' => 'image/vnd.net-fpx',
        'nsc' => 'application/x-conference',
        'nsf' => 'application/vnd.lotus-notes',
        'ntf' => 'application/vnd.nitf',
        'numbers' => 'application/x-iwork-numbers-sffnumbers',
        'nzb' => 'application/x-nzb',
        'oa2' => 'application/vnd.fujitsu.oasys2',
        'oa3' => 'application/vnd.fujitsu.oasys3',
        'oas' => 'application/vnd.fujitsu.oasys',
        'obd' => 'application/x-msbinder',
        'obj' => 'application/x-tgif',
        'oda' => 'application/oda',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odft' => 'application/vnd.oasis.opendocument.formula-template',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'oga' => 'audio/ogg',
        'ogg' => 'audio/ogg',
        'ogv' => 'video/ogg',
        'ogx' => 'application/ogg',
        'omdoc' => 'application/omdoc+xml',
        'onepkg' => 'application/onenote',
        'onetmp' => 'application/onenote',
        'onetoc' => 'application/onenote',
        'onetoc2' => 'application/onenote',
        'opf' => 'application/oebps-package+xml',
        'opml' => 'text/x-opml',
        'oprc' => 'application/vnd.palm',
        'org' => 'application/vnd.lotus-organizer',
        'osf' => 'application/vnd.yamaha.openscoreformat',
        'osfpvg' => 'application/vnd.yamaha.openscoreformat.osfpvg+xml',
        'otc' => 'application/vnd.oasis.opendocument.chart-template',
        'otf' => 'font/otf',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'oti' => 'application/vnd.oasis.opendocument.image-template',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oxps' => 'application/oxps',
        'oxt' => 'application/vnd.openofficeorg.extension',
        'p' => 'text/x-pascal',
        'p10' => 'application/pkcs10',
        'p12' => 'application/x-pkcs12',
        'p7b' => 'application/x-pkcs7-certificates',
        'p7c' => 'application/pkcs7-mime',
        'p7m' => 'application/pkcs7-mime',
        'p7r' => 'application/x-pkcs7-certreqresp',
        'p7s' => 'application/pkcs7-signature',
        'p8' => 'application/pkcs8',
        'pages' => 'application/x-iwork-pages-sffpages',
        'pas' => 'text/x-pascal',
        'paw' => 'application/vnd.pawaafile',
        'pbd' => 'application/vnd.powerbuilder6',
        'pbm' => 'image/x-portable-bitmap',
        'pcap' => 'application/vnd.tcpdump.pcap',
        'pcf' => 'application/x-font-pcf',
        'pcl' => 'application/vnd.hp-pcl',
        'pclxl' => 'application/vnd.hp-pclxl',
        'pct' => 'image/x-pict',
        'pcurl' => 'application/vnd.curl.pcurl',
        'pcx' => 'image/x-pcx',
        'pdb' => 'application/vnd.palm',
        'pdf' => 'application/pdf',
        'pfa' => 'application/x-font-type1',
        'pfb' => 'application/x-font-type1',
        'pfm' => 'application/x-font-type1',
        'pfr' => 'application/font-tdpfr',
        'pfx' => 'application/x-pkcs12',
        'pgm' => 'image/x-portable-graymap',
        'pgn' => 'application/x-chess-pgn',
        'pgp' => 'application/pgp-encrypted',
        'pic' => 'image/x-pict',
        'pkg' => 'application/octet-stream',
        'pki' => 'application/pkixcmp',
        'pkipath' => 'application/pkix-pkipath',
        'plb' => 'application/vnd.3gpp.pic-bw-large',
        'plc' => 'application/vnd.mobius.plc',
        'plf' => 'application/vnd.pocketlearn',
        'pls' => 'application/pls+xml',
        'pml' => 'application/vnd.ctc-posml',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'portpkg' => 'application/vnd.macports.portpkg',
        'pot' => 'application/vnd.ms-powerpoint',
        'potm' => 'application/vnd.ms-powerpoint.template.macroenabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroenabled.12',
        'ppd' => 'application/vnd.cups-ppd',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroenabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroenabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pqa' => 'application/vnd.palm',
        'prc' => 'application/x-mobipocket-ebook',
        'pre' => 'application/vnd.lotus-freelance',
        'prf' => 'application/pics-rules',
        'ps' => 'application/postscript',
        'psb' => 'application/vnd.3gpp.pic-bw-small',
        'psd' => 'image/vnd.adobe.photoshop',
        'psf' => 'application/x-font-linux-psf',
        'pskcxml' => 'application/pskc+xml',
        'ptid' => 'application/vnd.pvi.ptid1',
        'pub' => 'application/x-mspublisher',
        'pvb' => 'application/vnd.3gpp.pic-bw-var',
        'pwn' => 'application/vnd.3m.post-it-notes',
        'pya' => 'audio/vnd.ms-playready.media.pya',
        'pyv' => 'video/vnd.ms-playready.media.pyv',
        'qam' => 'application/vnd.epson.quickanime',
        'qbo' => 'application/vnd.intu.qbo',
        'qfx' => 'application/vnd.intu.qfx',
        'qps' => 'application/vnd.publishare-delta-tree',
        'qt' => 'video/quicktime',
        'qwd' => 'application/vnd.quark.quarkxpress',
        'qwt' => 'application/vnd.quark.quarkxpress',
        'qxb' => 'application/vnd.quark.quarkxpress',
        'qxd' => 'application/vnd.quark.quarkxpress',
        'qxl' => 'application/vnd.quark.quarkxpress',
        'qxt' => 'application/vnd.quark.quarkxpress',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rar' => 'application/x-rar-compressed',
        'ras' => 'image/x-cmu-raster',
        'rcprofile' => 'application/vnd.ipunplugged.rcprofile',
        'rdf' => 'application/rdf+xml',
        'rdz' => 'application/vnd.data-vision.rdz',
        'rep' => 'application/vnd.businessobjects',
        'res' => 'application/x-dtbresource+xml',
        'rgb' => 'image/x-rgb',
        'rif' => 'application/reginfo+xml',
        'rip' => 'audio/vnd.rip',
        'ris' => 'application/x-research-info-systems',
        'rl' => 'application/resource-lists+xml',
        'rlc' => 'image/vnd.fujixerox.edmics-rlc',
        'rld' => 'application/resource-lists-diff+xml',
        'rm' => 'application/vnd.rn-realmedia',
        'rmi' => 'audio/midi',
        'rmp' => 'audio/x-pn-realaudio-plugin',
        'rms' => 'application/vnd.jcp.javame.midlet-rms',
        'rmvb' => 'application/vnd.rn-realmedia-vbr',
        'rnc' => 'application/relax-ng-compact-syntax',
        'roa' => 'application/rpki-roa',
        'roff' => 'text/troff',
        'rp9' => 'application/vnd.cloanto.rp9',
        'rpss' => 'application/vnd.nokia.radio-presets',
        'rpst' => 'application/vnd.nokia.radio-preset',
        'rq' => 'application/sparql-query',
        'rs' => 'application/rls-services+xml',
        'rsd' => 'application/rsd+xml',
        'rss' => 'application/rss+xml',
        'rtf' => 'application/rtf',
        'rtx' => 'text/richtext',
        's' => 'text/x-asm',
        's3m' => 'audio/s3m',
        'saf' => 'application/vnd.yamaha.smaf-audio',
        'sbml' => 'application/sbml+xml',
        'sc' => 'application/vnd.ibm.secure-container',
        'scd' => 'application/x-msschedule',
        'scm' => 'application/vnd.lotus-screencam',
        'scq' => 'application/scvp-cv-request',
        'scs' => 'application/scvp-cv-response',
        'scurl' => 'text/vnd.curl.scurl',
        'sda' => 'application/vnd.stardivision.draw',
        'sdc' => 'application/vnd.stardivision.calc',
        'sdd' => 'application/vnd.stardivision.impress',
        'sdkd' => 'application/vnd.solent.sdkm+xml',
        'sdkm' => 'application/vnd.solent.sdkm+xml',
        'sdp' => 'application/sdp',
        'sdw' => 'application/vnd.stardivision.writer',
        'see' => 'application/vnd.seemail',
        'seed' => 'application/vnd.fdsn.seed',
        'sema' => 'application/vnd.sema',
        'semd' => 'application/vnd.semd',
        'semf' => 'application/vnd.semf',
        'ser' => 'application/java-serialized-object',
        'setpay' => 'application/set-payment-initiation',
        'setreg' => 'application/set-registration-initiation',
        'sfd-hdstx' => 'application/vnd.hydrostatix.sof-data',
        'sfs' => 'application/vnd.spotfire.sfs',
        'sfv' => 'text/x-sfv',
        'sgi' => 'image/sgi',
        'sgl' => 'application/vnd.stardivision.writer-global',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'shf' => 'application/shf+xml',
        'sid' => 'image/x-mrsid-image',
        'sig' => 'application/pgp-signature',
        'sil' => 'audio/silk',
        'silo' => 'model/mesh',
        'sis' => 'application/vnd.symbian.install',
        'sisx' => 'application/vnd.symbian.install',
        'sit' => 'application/x-stuffit',
        'sitx' => 'application/x-stuffitx',
        'skd' => 'application/vnd.koan',
        'skm' => 'application/vnd.koan',
        'skp' => 'application/vnd.koan',
        'skt' => 'application/vnd.koan',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroenabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'slt' => 'application/vnd.epson.salt',
        'sm' => 'application/vnd.stepmania.stepchart',
        'smf' => 'application/vnd.stardivision.math',
        'smi' => 'application/smil+xml',
        'smil' => 'application/smil+xml',
        'smv' => 'video/x-smv',
        'smzip' => 'application/vnd.stepmania.package',
        'snd' => 'audio/basic',
        'snf' => 'application/x-font-snf',
        'so' => 'application/octet-stream',
        'spc' => 'application/x-pkcs7-certificates',
        'spf' => 'application/vnd.yamaha.smaf-phrase',
        'spl' => 'application/x-futuresplash',
        'spot' => 'text/vnd.in3d.spot',
        'spp' => 'application/scvp-vp-response',
        'spq' => 'application/scvp-vp-request',
        'spx' => 'audio/ogg',
        'sql' => 'application/x-sql',
        'src' => 'application/x-wais-source',
        'srt' => 'application/x-subrip',
        'sru' => 'application/sru+xml',
        'srx' => 'application/sparql-results+xml',
        'ssdl' => 'application/ssdl+xml',
        'sse' => 'application/vnd.kodak-descriptor',
        'ssf' => 'application/vnd.epson.ssf',
        'ssml' => 'application/ssml+xml',
        'st' => 'application/vnd.sailingtracker.track',
        'stc' => 'application/vnd.sun.xml.calc.template',
        'std' => 'application/vnd.sun.xml.draw.template',
        'stf' => 'application/vnd.wt.stf',
        'sti' => 'application/vnd.sun.xml.impress.template',
        'stk' => 'application/hyperstudio',
        'stl' => 'application/vnd.ms-pki.stl',
        'str' => 'application/vnd.pg.format',
        'stw' => 'application/vnd.sun.xml.writer.template',
        'sub' => [
            'image/vnd.dvb.subtitle',
            'text/vnd.dvb.subtitle'
        ],
        'sus' => 'application/vnd.sus-calendar',
        'susp' => 'application/vnd.sus-calendar',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svc' => 'application/vnd.dvb.service',
        'svd' => 'application/vnd.svd',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'swa' => 'application/x-director',
        'swf' => 'application/x-shockwave-flash',
        'swi' => 'application/vnd.aristanetworks.swi',
        'sxc' => 'application/vnd.sun.xml.calc',
        'sxd' => 'application/vnd.sun.xml.draw',
        'sxg' => 'application/vnd.sun.xml.writer.global',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sxm' => 'application/vnd.sun.xml.math',
        'sxw' => 'application/vnd.sun.xml.writer',
        't' => 'text/troff',
        't3' => 'application/x-t3vm-image',
        'taglet' => 'application/vnd.mynfc',
        'tao' => 'application/vnd.tao.intent-module-archive',
        'tar' => 'application/x-tar',
        'tcap' => 'application/vnd.3gpp2.tcap',
        'tcl' => 'application/x-tcl',
        'teacher' => 'application/vnd.smart.teacher',
        'tei' => 'application/tei+xml',
        'teicorpus' => 'application/tei+xml',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'text' => 'text/plain',
        'tfi' => 'application/thraud+xml',
        'tfm' => 'application/x-tex-tfm',
        'tga' => 'image/x-tga',
        'thmx' => 'application/vnd.ms-officetheme',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tmo' => 'application/vnd.tmobile-livetv',
        'torrent' => 'application/x-bittorrent',
        'tpl' => 'application/vnd.groove-tool-template',
        'tpt' => 'application/vnd.trid.tpt',
        'tr' => 'text/troff',
        'tra' => 'application/vnd.trueapp',
        'trm' => 'application/x-msterminal',
        'tsd' => 'application/timestamped-data',
        'tsv' => 'text/tab-separated-values',
        'ttc' => 'font/collection',
        'ttf' => 'font/ttf',
        'ttl' => 'text/turtle',
        'twd' => 'application/vnd.simtech-mindmapper',
        'twds' => 'application/vnd.simtech-mindmapper',
        'txd' => 'application/vnd.genomatix.tuxedo',
        'txf' => 'application/vnd.mobius.txf',
        'txt' => 'text/plain',
        'u32' => 'application/x-authorware-bin',
        'udeb' => 'application/x-debian-package',
        'ufd' => 'application/vnd.ufdl',
        'ufdl' => 'application/vnd.ufdl',
        'ulx' => 'application/x-glulx',
        'umj' => 'application/vnd.umajin',
        'unityweb' => 'application/vnd.unity',
        'uoml' => 'application/vnd.uoml+xml',
        'uri' => 'text/uri-list',
        'uris' => 'text/uri-list',
        'urls' => 'text/uri-list',
        'ustar' => 'application/x-ustar',
        'utz' => 'application/vnd.uiq.theme',
        'uu' => 'text/x-uuencode',
        'uva' => 'audio/vnd.dece.audio',
        'uvd' => 'application/vnd.dece.data',
        'uvf' => 'application/vnd.dece.data',
        'uvg' => 'image/vnd.dece.graphic',
        'uvh' => 'video/vnd.dece.hd',
        'uvi' => 'image/vnd.dece.graphic',
        'uvm' => 'video/vnd.dece.mobile',
        'uvp' => 'video/vnd.dece.pd',
        'uvs' => 'video/vnd.dece.sd',
        'uvt' => 'application/vnd.dece.ttml+xml',
        'uvu' => 'video/vnd.uvvu.mp4',
        'uvv' => 'video/vnd.dece.video',
        'uvva' => 'audio/vnd.dece.audio',
        'uvvd' => 'application/vnd.dece.data',
        'uvvf' => 'application/vnd.dece.data',
        'uvvg' => 'image/vnd.dece.graphic',
        'uvvh' => 'video/vnd.dece.hd',
        'uvvi' => 'image/vnd.dece.graphic',
        'uvvm' => 'video/vnd.dece.mobile',
        'uvvp' => 'video/vnd.dece.pd',
        'uvvs' => 'video/vnd.dece.sd',
        'uvvt' => 'application/vnd.dece.ttml+xml',
        'uvvu' => 'video/vnd.uvvu.mp4',
        'uvvv' => 'video/vnd.dece.video',
        'uvvx' => 'application/vnd.dece.unspecified',
        'uvvz' => 'application/vnd.dece.zip',
        'uvx' => 'application/vnd.dece.unspecified',
        'uvz' => 'application/vnd.dece.zip',
        'vcard' => 'text/vcard',
        'vcd' => 'application/x-cdlink',
        'vcf' => 'text/x-vcard',
        'vcg' => 'application/vnd.groove-vcard',
        'vcs' => 'text/x-vcalendar',
        'vcx' => 'application/vnd.vcx',
        'vis' => 'application/vnd.visionary',
        'viv' => 'video/vnd.vivo',
        'vob' => 'video/x-ms-vob',
        'vor' => 'application/vnd.stardivision.writer',
        'vox' => 'application/x-authorware-bin',
        'vrml' => 'model/vrml',
        'vsd' => 'application/vnd.visio',
        'vsf' => 'application/vnd.vsf',
        'vss' => 'application/vnd.visio',
        'vst' => 'application/vnd.visio',
        'vsw' => 'application/vnd.visio',
        'vtu' => 'model/vnd.vtu',
        'vxml' => 'application/voicexml+xml',
        'w3d' => 'application/x-director',
        'wad' => 'application/x-doom',
        'wav' => [
            'audio/wav',
            'audio/x-wav',
        ],
        'wax' => 'audio/x-ms-wax',
        'wbmp' => 'image/vnd.wap.wbmp',
        'wbs' => 'application/vnd.criticaltools.wbs+xml',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wcm' => 'application/vnd.ms-works',
        'wdb' => 'application/vnd.ms-works',
        'wdp' => 'image/vnd.ms-photo',
        'weba' => 'audio/webm',
        'webm' => 'video/webm',
        'webp' => 'image/webp',
        'wg' => 'application/vnd.pmi.widget',
        'wgt' => 'application/widget',
        'wks' => 'application/vnd.ms-works',
        'wm' => 'video/x-ms-wm',
        'wma' => 'audio/x-ms-wma',
        'wmd' => 'application/x-ms-wmd',
        'wmf' => 'application/x-msmetafile',
        'wml' => 'text/vnd.wap.wml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wmz' => [
            'application/x-ms-wmz',
            'application/x-msmetafile'
        ],
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'wpd' => 'application/vnd.wordperfect',
        'wpl' => 'application/vnd.ms-wpl',
        'wps' => 'application/vnd.ms-works',
        'wqd' => 'application/vnd.wqd',
        'wri' => 'application/x-mswrite',
        'wrl' => 'model/vrml',
        'wsdl' => 'application/wsdl+xml',
        'wspolicy' => 'application/wspolicy+xml',
        'wtb' => 'application/vnd.webturbo',
        'wvx' => 'video/x-ms-wvx',
        'x32' => 'application/x-authorware-bin',
        'x3d' => 'model/x3d+xml',
        'x3db' => 'model/x3d+binary',
        'x3dbz' => 'model/x3d+binary',
        'x3dv' => 'model/x3d+vrml',
        'x3dvz' => 'model/x3d+vrml',
        'x3dz' => 'model/x3d+xml',
        'xaml' => 'application/xaml+xml',
        'xap' => 'application/x-silverlight-app',
        'xar' => 'application/vnd.xara',
        'xbap' => 'application/x-ms-xbap',
        'xbd' => 'application/vnd.fujixerox.docuworks.binder',
        'xbm' => 'image/x-xbitmap',
        'xdf' => 'application/xcap-diff+xml',
        'xdm' => 'application/vnd.syncml.dm+xml',
        'xdp' => 'application/vnd.adobe.xdp+xml',
        'xdssc' => 'application/dssc+xml',
        'xdw' => 'application/vnd.fujixerox.docuworks',
        'xenc' => 'application/xenc+xml',
        'xer' => 'application/patch-ops-error+xml',
        'xfdf' => 'application/vnd.adobe.xfdf',
        'xfdl' => 'application/vnd.xfdl',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xhvml' => 'application/xv+xml',
        'xif' => 'image/vnd.xiff',
        'xla' => 'application/vnd.ms-excel',
        'xlam' => 'application/vnd.ms-excel.addin.macroenabled.12',
        'xlc' => 'application/vnd.ms-excel',
        'xlf' => 'application/x-xliff+xml',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroenabled.12',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroenabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlt' => 'application/vnd.ms-excel',
        'xltm' => 'application/vnd.ms-excel.template.macroenabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xlw' => 'application/vnd.ms-excel',
        'xm' => 'audio/xm',
        'xml' => [
            'text/xml',
            'application/xml',
        ],
        'xo' => 'application/vnd.olpc-sugar',
        'xop' => 'application/xop+xml',
        'xpi' => 'application/x-xpinstall',
        'xpl' => 'application/xproc+xml',
        'xpm' => 'image/x-xpixmap',
        'xpr' => 'application/vnd.is-xpr',
        'xps' => 'application/vnd.ms-xpsdocument',
        'xpw' => 'application/vnd.intercon.formnet',
        'xpx' => 'application/vnd.intercon.formnet',
        'xsl' => 'application/xml',
        'xslt' => 'application/xslt+xml',
        'xsm' => 'application/vnd.syncml+xml',
        'xspf' => 'application/xspf+xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'xvm' => 'application/xv+xml',
        'xvml' => 'application/xv+xml',
        'xwd' => 'image/x-xwindowdump',
        'xyz' => 'chemical/x-xyz',
        'xz' => 'application/x-xz',
        'yang' => 'application/yang',
        'yin' => 'application/yin+xml',
        'z1' => 'application/x-zmachine',
        'z2' => 'application/x-zmachine',
        'z3' => 'application/x-zmachine',
        'z4' => 'application/x-zmachine',
        'z5' => 'application/x-zmachine',
        'z6' => 'application/x-zmachine',
        'z7' => 'application/x-zmachine',
        'z8' => 'application/x-zmachine',
        'zaz' => 'application/vnd.zzazz.deck+xml',
        'zip' => 'application/zip',
        'zir' => 'application/vnd.zul',
        'zirz' => 'application/vnd.zul',
        'zmm' => 'application/vnd.handheld-entertainment+xml'
    ];
    
    /**
     * Maps MIME types to their corresponding file extensions.
     */
    const MIME_TYPE_FILE_EXTENSIONS_MAP = [
        'application/vnd.lotus-1-2-3' =>
        [
            0 => '123',
        ],
        'text/vnd.in3d.3dml' =>
        [
            0 => '3dml',
        ],
        'image/x-3ds' =>
        [
            0 => '3ds',
        ],
        'video/3gpp2' =>
        [
            0 => '3g2',
        ],
        'video/3gpp' =>
        [
            0 => '3gp',
        ],
        'application/x-7z-compressed' =>
        [
            0 => '7z',
        ],
        'application/x-authorware-bin' =>
        [
            0 => 'aab',
            1 => 'u32',
            2 => 'vox',
            3 => 'x32',
        ],
        'audio/x-aac' =>
        [
            0 => 'aac',
        ],
        'application/x-authorware-map' =>
        [
            0 => 'aam',
        ],
        'application/x-authorware-seg' =>
        [
            0 => 'aas',
        ],
        'application/x-abiword' =>
        [
            0 => 'abw',
        ],
        'application/pkix-attr-cert' =>
        [
            0 => 'ac',
        ],
        'application/vnd.americandynamics.acc' =>
        [
            0 => 'acc',
        ],
        'application/x-ace-compressed' =>
        [
            0 => 'ace',
        ],
        'application/vnd.acucobol' =>
        [
            0 => 'acu',
        ],
        'application/vnd.acucorp' =>
        [
            0 => 'acutc',
            1 => 'atc',
        ],
        'audio/adpcm' =>
        [
            0 => 'adp',
        ],
        'application/vnd.audiograph' =>
        [
            0 => 'aep',
        ],
        'application/x-font-type1' =>
        [
            0 => 'afm',
            1 => 'pfa',
            2 => 'pfb',
            3 => 'pfm',
        ],
        'application/vnd.ibm.modcap' =>
        [
            0 => 'afp',
            1 => 'list3820',
            2 => 'listafp',
        ],
        'application/vnd.ahead.space' =>
        [
            0 => 'ahead',
        ],
        'application/postscript' =>
        [
            0 => 'ai',
            1 => 'eps',
            2 => 'ps',
        ],
        'audio/x-aiff' =>
        [
            0 => 'aif',
            1 => 'aifc',
            2 => 'aiff',
        ],
        'application/vnd.adobe.air-application-installer-package+zip' =>
        [
            0 => 'air',
        ],
        'application/vnd.dvb.ait' =>
        [
            0 => 'ait',
        ],
        'application/vnd.amiga.ami' =>
        [
            0 => 'ami',
        ],
        'application/vnd.android.package-archive' =>
        [
            0 => 'apk',
        ],
        'text/cache-manifest' =>
        [
            0 => 'appcache',
        ],
        'application/x-ms-application' =>
        [
            0 => 'application',
        ],
        'application/vnd.lotus-approach' =>
        [
            0 => 'apr',
        ],
        'application/x-freearc' =>
        [
            0 => 'arc',
        ],
        'application/pgp-signature' =>
        [
            0 => 'asc',
            1 => 'sig',
        ],
        'video/x-ms-asf' =>
        [
            0 => 'asf',
            1 => 'asx',
        ],
        'text/xml' =>
        [
            0 => 'xml',
        ],
        'text/x-asm' =>
        [
            0 => 'asm',
            1 => 's',
        ],
        'application/vnd.accpac.simply.aso' =>
        [
            0 => 'aso',
        ],
        'application/atom+xml' =>
        [
            0 => 'atom',
        ],
        'application/atomcat+xml' =>
        [
            0 => 'atomcat',
        ],
        'application/atomsvc+xml' =>
        [
            0 => 'atomsvc',
        ],
        'application/vnd.antix.game-component' =>
        [
            0 => 'atx',
        ],
        'audio/basic' =>
        [
            0 => 'au',
            1 => 'snd',
        ],
        'video/x-msvideo' =>
        [
            0 => 'avi',
        ],
        'video/avi' =>
        [
            0 => 'avi',
        ],
        'application/applixware' =>
        [
            0 => 'aw',
        ],
        'application/vnd.airzip.filesecure.azf' =>
        [
            0 => 'azf',
        ],
        'application/vnd.airzip.filesecure.azs' =>
        [
            0 => 'azs',
        ],
        'application/vnd.amazon.ebook' =>
        [
            0 => 'azw',
        ],
        'application/x-msdownload' =>
        [
            0 => 'bat',
            1 => 'com',
            2 => 'dll',
            3 => 'exe',
            4 => 'msi',
        ],
        'application/x-bcpio' =>
        [
            0 => 'bcpio',
        ],
        'application/x-font-bdf' =>
        [
            0 => 'bdf',
        ],
        'application/vnd.syncml.dm+wbxml' =>
        [
            0 => 'bdm',
        ],
        'application/vnd.realvnc.bed' =>
        [
            0 => 'bed',
        ],
        'application/vnd.fujitsu.oasysprs' =>
        [
            0 => 'bh2',
        ],
        'application/octet-stream' =>
        [
            0 => 'bin',
            1 => 'bpk',
            2 => 'deploy',
            3 => 'dist',
            4 => 'distz',
            5 => 'dms',
            6 => 'dump',
            7 => 'elc',
            8 => 'lrf',
            9 => 'mar',
            10 => 'pkg',
            11 => 'so',
        ],
        'application/x-blorb' =>
        [
            0 => 'blb',
            1 => 'blorb',
        ],
        'application/vnd.bmi' =>
        [
            0 => 'bmi',
        ],
        'image/bmp' =>
        [
            0 => 'bmp',
        ],
        'application/vnd.framemaker' =>
        [
            0 => 'book',
            1 => 'fm',
            2 => 'frame',
            3 => 'maker',
        ],
        'application/vnd.previewsystems.box' =>
        [
            0 => 'box',
        ],
        'application/x-bzip2' =>
        [
            0 => 'boz',
            1 => 'bz2',
        ],
        'image/prs.btif' =>
        [
            0 => 'btif',
        ],
        'application/x-bzip' =>
        [
            0 => 'bz',
        ],
        'text/x-c' =>
        [
            0 => 'c',
            1 => 'cc',
            2 => 'cpp',
            3 => 'cxx',
            4 => 'dic',
            5 => 'h',
            6 => 'hh',
        ],
        'application/vnd.cluetrust.cartomobile-config' =>
        [
            0 => 'c11amc',
        ],
        'application/vnd.cluetrust.cartomobile-config-pkg' =>
        [
            0 => 'c11amz',
        ],
        'application/vnd.clonk.c4group' =>
        [
            0 => 'c4d',
            1 => 'c4f',
            2 => 'c4g',
            3 => 'c4p',
            4 => 'c4u',
        ],
        'application/vnd.ms-cab-compressed' =>
        [
            0 => 'cab',
        ],
        'audio/x-caf' =>
        [
            0 => 'caf',
        ],
        'application/vnd.tcpdump.pcap' =>
        [
            0 => 'cap',
            1 => 'dmp',
            2 => 'pcap',
        ],
        'application/vnd.curl.car' =>
        [
            0 => 'car',
        ],
        'application/vnd.ms-pki.seccat' =>
        [
            0 => 'cat',
        ],
        'application/x-cbr' =>
        [
            0 => 'cb7',
            1 => 'cba',
            2 => 'cbr',
            3 => 'cbt',
            4 => 'cbz',
        ],
        'application/x-director' =>
        [
            0 => 'cct',
            1 => 'cst',
            2 => 'cxt',
            3 => 'dcr',
            4 => 'dir',
            5 => 'dxr',
            6 => 'fgd',
            7 => 'swa',
            8 => 'w3d',
        ],
        'application/ccxml+xml' =>
        [
            0 => 'ccxml',
        ],
        'application/vnd.contact.cmsg' =>
        [
            0 => 'cdbcmsg',
        ],
        'application/x-netcdf' =>
        [
            0 => 'cdf',
            1 => 'nc',
        ],
        'application/vnd.mediastation.cdkey' =>
        [
            0 => 'cdkey',
        ],
        'application/cdmi-capability' =>
        [
            0 => 'cdmia',
        ],
        'application/cdmi-container' =>
        [
            0 => 'cdmic',
        ],
        'application/cdmi-domain' =>
        [
            0 => 'cdmid',
        ],
        'application/cdmi-object' =>
        [
            0 => 'cdmio',
        ],
        'application/cdmi-queue' =>
        [
            0 => 'cdmiq',
        ],
        'chemical/x-cdx' =>
        [
            0 => 'cdx',
        ],
        'application/vnd.chemdraw+xml' =>
        [
            0 => 'cdxml',
        ],
        'application/vnd.cinderella' =>
        [
            0 => 'cdy',
        ],
        'application/pkix-cert' =>
        [
            0 => 'cer',
        ],
        'application/x-cfs-compressed' =>
        [
            0 => 'cfs',
        ],
        'image/cgm' =>
        [
            0 => 'cgm',
        ],
        'application/x-chat' =>
        [
            0 => 'chat',
        ],
        'application/vnd.ms-htmlhelp' =>
        [
            0 => 'chm',
        ],
        'application/vnd.kde.kchart' =>
        [
            0 => 'chrt',
        ],
        'chemical/x-cif' =>
        [
            0 => 'cif',
        ],
        'application/vnd.anser-web-certificate-issue-initiation' =>
        [
            0 => 'cii',
        ],
        'application/vnd.ms-artgalry' =>
        [
            0 => 'cil',
        ],
        'application/vnd.claymore' =>
        [
            0 => 'cla',
        ],
        'application/java-vm' =>
        [
            0 => 'class',
        ],
        'application/vnd.crick.clicker.keyboard' =>
        [
            0 => 'clkk',
        ],
        'application/vnd.crick.clicker.palette' =>
        [
            0 => 'clkp',
        ],
        'application/vnd.crick.clicker.template' =>
        [
            0 => 'clkt',
        ],
        'application/vnd.crick.clicker.wordbank' =>
        [
            0 => 'clkw',
        ],
        'application/vnd.crick.clicker' =>
        [
            0 => 'clkx',
        ],
        'application/x-msclip' =>
        [
            0 => 'clp',
        ],
        'application/vnd.cosmocaller' =>
        [
            0 => 'cmc',
        ],
        'chemical/x-cmdf' =>
        [
            0 => 'cmdf',
        ],
        'chemical/x-cml' =>
        [
            0 => 'cml',
        ],
        'application/vnd.yellowriver-custom-menu' =>
        [
            0 => 'cmp',
        ],
        'image/x-cmx' =>
        [
            0 => 'cmx',
        ],
        'application/vnd.rim.cod' =>
        [
            0 => 'cod',
        ],
        'text/plain' =>
        [
            0 => 'conf',
            1 => 'def',
            2 => 'in',
            3 => 'list',
            4 => 'log',
            5 => 'text',
            6 => 'txt',
        ],
        'application/x-cpio' =>
        [
            0 => 'cpio',
        ],
        'application/mac-compactpro' =>
        [
            0 => 'cpt',
        ],
        'application/x-mscardfile' =>
        [
            0 => 'crd',
        ],
        'application/pkix-crl' =>
        [
            0 => 'crl',
        ],
        'application/x-x509-ca-cert' =>
        [
            0 => 'crt',
            1 => 'der',
        ],
        'application/vnd.rig.cryptonote' =>
        [
            0 => 'cryptonote',
        ],
        'application/x-csh' =>
        [
            0 => 'csh',
        ],
        'chemical/x-csml' =>
        [
            0 => 'csml',
        ],
        'application/vnd.commonspace' =>
        [
            0 => 'csp',
        ],
        'text/css' =>
        [
            0 => 'css',
        ],
        'text/csv' =>
        [
            0 => 'csv',
        ],
        'application/cu-seeme' =>
        [
            0 => 'cu',
        ],
        'text/vnd.curl' =>
        [
            0 => 'curl',
        ],
        'application/prs.cww' =>
        [
            0 => 'cww',
        ],
        'model/vnd.collada+xml' =>
        [
            0 => 'dae',
        ],
        'application/vnd.mobius.daf' =>
        [
            0 => 'daf',
        ],
        'application/vnd.dart' =>
        [
            0 => 'dart',
        ],
        'application/vnd.fdsn.seed' =>
        [
            0 => 'dataless',
            1 => 'seed',
        ],
        'application/davmount+xml' =>
        [
            0 => 'davmount',
        ],
        'application/docbook+xml' =>
        [
            0 => 'dbk',
        ],
        'text/vnd.curl.dcurl' =>
        [
            0 => 'dcurl',
        ],
        'application/vnd.oma.dd2+xml' =>
        [
            0 => 'dd2',
        ],
        'application/vnd.fujixerox.ddd' =>
        [
            0 => 'ddd',
        ],
        'application/x-debian-package' =>
        [
            0 => 'deb',
            1 => 'udeb',
        ],
        'application/vnd.dreamfactory' =>
        [
            0 => 'dfac',
        ],
        'application/x-dgc-compressed' =>
        [
            0 => 'dgc',
        ],
        'application/vnd.mobius.dis' =>
        [
            0 => 'dis',
        ],
        'image/vnd.djvu' =>
        [
            0 => 'djv',
            1 => 'djvu',
        ],
        'application/x-apple-diskimage' =>
        [
            0 => 'dmg',
        ],
        'application/vnd.dna' =>
        [
            0 => 'dna',
        ],
        'application/msword' =>
        [
            0 => 'doc',
            1 => 'dot',
        ],
        'application/vnd.ms-word.document.macroenabled.12' =>
        [
            0 => 'docm',
        ],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' =>
        [
            0 => 'docx',
        ],
        'application/vnd.ms-word.template.macroenabled.12' =>
        [
            0 => 'dotm',
        ],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template' =>
        [
            0 => 'dotx',
        ],
        'application/vnd.osgi.dp' =>
        [
            0 => 'dp',
        ],
        'application/vnd.dpgraph' =>
        [
            0 => 'dpg',
        ],
        'audio/vnd.dra' =>
        [
            0 => 'dra',
        ],
        'text/prs.lines.tag' =>
        [
            0 => 'dsc',
        ],
        'application/dssc+der' =>
        [
            0 => 'dssc',
        ],
        'application/x-dtbook+xml' =>
        [
            0 => 'dtb',
        ],
        'application/xml-dtd' =>
        [
            0 => 'dtd',
        ],
        'audio/vnd.dts' =>
        [
            0 => 'dts',
        ],
        'audio/vnd.dts.hd' =>
        [
            0 => 'dtshd',
        ],
        'video/vnd.dvb.file' =>
        [
            0 => 'dvb',
        ],
        'application/x-dvi' =>
        [
            0 => 'dvi',
        ],
        'model/vnd.dwf' =>
        [
            0 => 'dwf',
        ],
        'image/vnd.dwg' =>
        [
            0 => 'dwg',
        ],
        'image/vnd.dxf' =>
        [
            0 => 'dxf',
        ],
        'application/vnd.spotfire.dxp' =>
        [
            0 => 'dxp',
        ],
        'audio/vnd.nuera.ecelp4800' =>
        [
            0 => 'ecelp4800',
        ],
        'audio/vnd.nuera.ecelp7470' =>
        [
            0 => 'ecelp7470',
        ],
        'audio/vnd.nuera.ecelp9600' =>
        [
            0 => 'ecelp9600',
        ],
        'application/ecmascript' =>
        [
            0 => 'ecma',
        ],
        'application/vnd.novadigm.edm' =>
        [
            0 => 'edm',
        ],
        'application/vnd.novadigm.edx' =>
        [
            0 => 'edx',
        ],
        'application/vnd.picsel' =>
        [
            0 => 'efif',
        ],
        'application/vnd.pg.osasli' =>
        [
            0 => 'ei6',
        ],
        'application/x-msmetafile' =>
        [
            0 => 'emf',
            1 => 'emz',
            2 => 'wmf',
            3 => 'wmz',
        ],
        'application/x-ms-wmz' =>
        [
            0 => 'wmz'
        ],
        'message/rfc822' =>
        [
            0 => 'eml',
            1 => 'mime',
        ],
        'application/emma+xml' =>
        [
            0 => 'emma',
        ],
        'audio/vnd.digital-winds' =>
        [
            0 => 'eol',
        ],
        'application/vnd.ms-fontobject' =>
        [
            0 => 'eot',
        ],
        'application/epub+zip' =>
        [
            0 => 'epub',
        ],
        'application/vnd.eszigno3+xml' =>
        [
            0 => 'es3',
            1 => 'et3',
        ],
        'application/vnd.osgi.subsystem' =>
        [
            0 => 'esa',
        ],
        'application/vnd.epson.esf' =>
        [
            0 => 'esf',
        ],
        'text/x-setext' =>
        [
            0 => 'etx',
        ],
        'application/x-eva' =>
        [
            0 => 'eva',
        ],
        'application/x-envoy' =>
        [
            0 => 'evy',
        ],
        'application/exi' =>
        [
            0 => 'exi',
        ],
        'application/vnd.novadigm.ext' =>
        [
            0 => 'ext',
        ],
        'application/andrew-inset' =>
        [
            0 => 'ez',
        ],
        'application/vnd.ezpix-album' =>
        [
            0 => 'ez2',
        ],
        'application/vnd.ezpix-package' =>
        [
            0 => 'ez3',
        ],
        'text/x-fortran' =>
        [
            0 => 'f',
            1 => 'f77',
            2 => 'f90',
            3 => 'for',
        ],
        'video/x-f4v' =>
        [
            0 => 'f4v',
        ],
        'image/vnd.fastbidsheet' =>
        [
            0 => 'fbs',
        ],
        'application/vnd.adobe.formscentral.fcdt' =>
        [
            0 => 'fcdt',
        ],
        'application/vnd.isac.fcs' =>
        [
            0 => 'fcs',
        ],
        'application/vnd.fdf' =>
        [
            0 => 'fdf',
        ],
        'application/vnd.denovo.fcselayout-link' =>
        [
            0 => 'fe_launch',
        ],
        'application/vnd.fujitsu.oasysgp' =>
        [
            0 => 'fg5',
        ],
        'image/x-freehand' =>
        [
            0 => 'fh',
            1 => 'fh4',
            2 => 'fh5',
            3 => 'fh7',
            4 => 'fhc',
        ],
        'application/x-xfig' =>
        [
            0 => 'fig',
        ],
        'audio/x-flac' =>
        [
            0 => 'flac',
        ],
        'video/x-fli' =>
        [
            0 => 'fli',
        ],
        'application/vnd.micrografx.flo' =>
        [
            0 => 'flo',
        ],
        'video/x-flv' =>
        [
            0 => 'flv',
        ],
        'application/vnd.kde.kivio' =>
        [
            0 => 'flw',
        ],
        'text/vnd.fmi.flexstor' =>
        [
            0 => 'flx',
        ],
        'text/vnd.fly' =>
        [
            0 => 'fly',
        ],
        'application/vnd.frogans.fnc' =>
        [
            0 => 'fnc',
        ],
        'image/vnd.fpx' =>
        [
            0 => 'fpx',
        ],
        'application/vnd.fsc.weblaunch' =>
        [
            0 => 'fsc',
        ],
        'image/vnd.fst' =>
        [
            0 => 'fst',
        ],
        'application/vnd.fluxtime.clip' =>
        [
            0 => 'ftc',
        ],
        'application/vnd.anser-web-funds-transfer-initiation' =>
        [
            0 => 'fti',
        ],
        'video/vnd.fvt' =>
        [
            0 => 'fvt',
        ],
        'application/vnd.adobe.fxp' =>
        [
            0 => 'fxp',
            1 => 'fxpl',
        ],
        'application/vnd.fuzzysheet' =>
        [
            0 => 'fzs',
        ],
        'application/vnd.geoplan' =>
        [
            0 => 'g2w',
        ],
        'image/g3fax' =>
        [
            0 => 'g3',
        ],
        'application/vnd.geospace' =>
        [
            0 => 'g3w',
        ],
        'application/vnd.groove-account' =>
        [
            0 => 'gac',
        ],
        'application/x-tads' =>
        [
            0 => 'gam',
        ],
        'application/rpki-ghostbusters' =>
        [
            0 => 'gbr',
        ],
        'application/x-gca-compressed' =>
        [
            0 => 'gca',
        ],
        'model/vnd.gdl' =>
        [
            0 => 'gdl',
        ],
        'application/vnd.dynageo' =>
        [
            0 => 'geo',
        ],
        'application/vnd.geometry-explorer' =>
        [
            0 => 'gex',
            1 => 'gre',
        ],
        'application/vnd.geogebra.file' =>
        [
            0 => 'ggb',
        ],
        'application/vnd.geogebra.tool' =>
        [
            0 => 'ggt',
        ],
        'application/vnd.groove-help' =>
        [
            0 => 'ghf',
        ],
        'image/gif' =>
        [
            0 => 'gif',
        ],
        'application/vnd.groove-identity-message' =>
        [
            0 => 'gim',
        ],
        'application/gml+xml' =>
        [
            0 => 'gml',
        ],
        'application/vnd.gmx' =>
        [
            0 => 'gmx',
        ],
        'application/x-gnumeric' =>
        [
            0 => 'gnumeric',
        ],
        'application/vnd.flographit' =>
        [
            0 => 'gph',
        ],
        'application/gpx+xml' =>
        [
            0 => 'gpx',
        ],
        'application/vnd.grafeq' =>
        [
            0 => 'gqf',
            1 => 'gqs',
        ],
        'application/srgs' =>
        [
            0 => 'gram',
        ],
        'application/x-gramps-xml' =>
        [
            0 => 'gramps',
        ],
        'application/vnd.groove-injector' =>
        [
            0 => 'grv',
        ],
        'application/srgs+xml' =>
        [
            0 => 'grxml',
        ],
        'application/x-font-ghostscript' =>
        [
            0 => 'gsf',
        ],
        'application/x-gtar' =>
        [
            0 => 'gtar',
        ],
        'application/vnd.groove-tool-message' =>
        [
            0 => 'gtm',
        ],
        'model/vnd.gtw' =>
        [
            0 => 'gtw',
        ],
        'text/vnd.graphviz' =>
        [
            0 => 'gv',
        ],
        'application/gxf' =>
        [
            0 => 'gxf',
        ],
        'application/vnd.geonext' =>
        [
            0 => 'gxt',
        ],
        'video/h261' =>
        [
            0 => 'h261',
        ],
        'video/h263' =>
        [
            0 => 'h263',
        ],
        'video/h264' =>
        [
            0 => 'h264',
        ],
        'application/vnd.hal+xml' =>
        [
            0 => 'hal',
        ],
        'application/vnd.hbci' =>
        [
            0 => 'hbci',
        ],
        'application/x-hdf' =>
        [
            0 => 'hdf',
        ],
        'application/winhlp' =>
        [
            0 => 'hlp',
        ],
        'application/vnd.hp-hpgl' =>
        [
            0 => 'hpgl',
        ],
        'application/vnd.hp-hpid' =>
        [
            0 => 'hpid',
        ],
        'application/vnd.hp-hps' =>
        [
            0 => 'hps',
        ],
        'application/mac-binhex40' =>
        [
            0 => 'hqx',
        ],
        'application/vnd.kenameaapp' =>
        [
            0 => 'htke',
        ],
        'text/html' =>
        [
            0 => 'htm',
            1 => 'html',
        ],
        'application/vnd.yamaha.hv-dic' =>
        [
            0 => 'hvd',
        ],
        'application/vnd.yamaha.hv-voice' =>
        [
            0 => 'hvp',
        ],
        'application/vnd.yamaha.hv-script' =>
        [
            0 => 'hvs',
        ],
        'application/vnd.intergeo' =>
        [
            0 => 'i2g',
        ],
        'application/vnd.iccprofile' =>
        [
            0 => 'icc',
            1 => 'icm',
        ],
        'x-conference/x-cooltalk' =>
        [
            0 => 'ice',
        ],
        'image/x-icon' =>
        [
            0 => 'ico',
        ],
        'text/calendar' =>
        [
            0 => 'ics',
            1 => 'ifb',
        ],
        'image/ief' =>
        [
            0 => 'ief',
        ],
        'application/vnd.shana.informed.formdata' =>
        [
            0 => 'ifm',
        ],
        'model/iges' =>
        [
            0 => 'iges',
            1 => 'igs',
        ],
        'application/vnd.igloader' =>
        [
            0 => 'igl',
        ],
        'application/vnd.insors.igm' =>
        [
            0 => 'igm',
        ],
        'application/vnd.micrografx.igx' =>
        [
            0 => 'igx',
        ],
        'application/vnd.shana.informed.interchange' =>
        [
            0 => 'iif',
        ],
        'application/vnd.accpac.simply.imp' =>
        [
            0 => 'imp',
        ],
        'application/vnd.ms-ims' =>
        [
            0 => 'ims',
        ],
        'application/inkml+xml' =>
        [
            0 => 'ink',
            1 => 'inkml',
        ],
        'application/x-install-instructions' =>
        [
            0 => 'install',
        ],
        'application/vnd.astraea-software.iota' =>
        [
            0 => 'iota',
        ],
        'application/ipfix' =>
        [
            0 => 'ipfix',
        ],
        'application/vnd.shana.informed.package' =>
        [
            0 => 'ipk',
        ],
        'application/vnd.ibm.rights-management' =>
        [
            0 => 'irm',
        ],
        'application/vnd.irepository.package+xml' =>
        [
            0 => 'irp',
        ],
        'application/x-iso9660-image' =>
        [
            0 => 'iso',
        ],
        'application/vnd.shana.informed.formtemplate' =>
        [
            0 => 'itp',
        ],
        'application/vnd.immervision-ivp' =>
        [
            0 => 'ivp',
        ],
        'application/vnd.immervision-ivu' =>
        [
            0 => 'ivu',
        ],
        'text/vnd.sun.j2me.app-descriptor' =>
        [
            0 => 'jad',
        ],
        'application/vnd.jam' =>
        [
            0 => 'jam',
        ],
        'application/java-archive' =>
        [
            0 => 'jar',
        ],
        'text/x-java-source' =>
        [
            0 => 'java',
        ],
        'application/vnd.jisp' =>
        [
            0 => 'jisp',
        ],
        'application/vnd.hp-jlyt' =>
        [
            0 => 'jlt',
        ],
        'application/x-java-jnlp-file' =>
        [
            0 => 'jnlp',
        ],
        'application/vnd.joost.joda-archive' =>
        [
            0 => 'joda',
        ],
        'image/jpeg' =>
        [
            0 => 'jpe',
            1 => 'jpeg',
            2 => 'jpg',
        ],
        'video/jpm' =>
        [
            0 => 'jpgm',
            1 => 'jpm',
        ],
        'video/jpeg' =>
        [
            0 => 'jpgv',
        ],
        'application/javascript' =>
        [
            0 => 'js',
        ],
        'application/json' =>
        [
            0 => 'json',
        ],
        'application/jsonml+json' =>
        [
            0 => 'jsonml',
        ],
        'audio/midi' =>
        [
            0 => 'kar',
            1 => 'mid',
            2 => 'midi',
            3 => 'rmi',
        ],
        'application/vnd.kde.karbon' =>
        [
            0 => 'karbon',
        ],
        'application/x-iwork-keynote-sffkey' =>
        [
            0 => 'key'
        ],
        'application/vnd.kde.kformula' =>
        [
            0 => 'kfo',
        ],
        'application/vnd.kidspiration' =>
        [
            0 => 'kia',
        ],
        'application/vnd.google-earth.kml+xml' =>
        [
            0 => 'kml',
        ],
        'application/vnd.google-earth.kmz' =>
        [
            0 => 'kmz',
        ],
        'application/vnd.kinar' =>
        [
            0 => 'kne',
            1 => 'knp',
        ],
        'application/vnd.kde.kontour' =>
        [
            0 => 'kon',
        ],
        'application/vnd.kde.kpresenter' =>
        [
            0 => 'kpr',
            1 => 'kpt',
        ],
        'application/vnd.ds-keypoint' =>
        [
            0 => 'kpxx',
        ],
        'application/vnd.kde.kspread' =>
        [
            0 => 'ksp',
        ],
        'application/vnd.kahootz' =>
        [
            0 => 'ktr',
            1 => 'ktz',
        ],
        'image/ktx' =>
        [
            0 => 'ktx',
        ],
        'application/vnd.kde.kword' =>
        [
            0 => 'kwd',
            1 => 'kwt',
        ],
        'application/vnd.las.las+xml' =>
        [
            0 => 'lasxml',
        ],
        'application/x-latex' =>
        [
            0 => 'latex',
        ],
        'application/vnd.llamagraphics.life-balance.desktop' =>
        [
            0 => 'lbd',
        ],
        'application/vnd.llamagraphics.life-balance.exchange+xml' =>
        [
            0 => 'lbe',
        ],
        'application/vnd.hhe.lesson-player' =>
        [
            0 => 'les',
        ],
        'application/x-lzh-compressed' =>
        [
            0 => 'lha',
            1 => 'lzh',
        ],
        'application/vnd.route66.link66+xml' =>
        [
            0 => 'link66',
        ],
        'application/x-ms-shortcut' =>
        [
            0 => 'lnk',
        ],
        'application/lost+xml' =>
        [
            0 => 'lostxml',
        ],
        'application/vnd.ms-lrm' =>
        [
            0 => 'lrm',
        ],
        'application/vnd.frogans.ltf' =>
        [
            0 => 'ltf',
        ],
        'audio/vnd.lucent.voice' =>
        [
            0 => 'lvp',
        ],
        'application/vnd.lotus-wordpro' =>
        [
            0 => 'lwp',
        ],
        'application/x-msmediaview' =>
        [
            0 => 'm13',
            1 => 'm14',
            2 => 'mvb',
        ],
        'video/mpeg' =>
        [
            0 => 'm1v',
            1 => 'm2v',
            2 => 'mpe',
            3 => 'mpeg',
            4 => 'mpg',
        ],
        'application/mp21' =>
        [
            0 => 'm21',
            1 => 'mp21',
        ],
        'audio/mp3' =>
        [
            0 => 'mp3'
        ],
        'audio/mpeg' =>
        [
            0 => 'm2a',
            1 => 'm3a',
            2 => 'mp2',
            3 => 'mp2a',
            4 => 'mp3',
            5 => 'mpga',
        ],
        'audio/x-mpegurl' =>
        [
            0 => 'm3u',
        ],
        'application/vnd.apple.mpegurl' =>
        [
            0 => 'm3u8',
        ],
        'audio/mp4' =>
        [
            0 => 'm4a',
            1 => 'mp4a',
        ],
        'video/vnd.mpegurl' =>
        [
            0 => 'm4u',
            1 => 'mxu',
        ],
        'video/x-m4v' =>
        [
            0 => 'm4v',
        ],
        'application/mathematica' =>
        [
            0 => 'ma',
            1 => 'mb',
            2 => 'nb',
        ],
        'application/mads+xml' =>
        [
            0 => 'mads',
        ],
        'application/vnd.ecowin.chart' =>
        [
            0 => 'mag',
        ],
        'text/troff' =>
        [
            0 => 'man',
            1 => 'me',
            2 => 'ms',
            3 => 'roff',
            4 => 't',
            5 => 'tr',
        ],
        'application/mathml+xml' =>
        [
            0 => 'mathml',
        ],
        'application/vnd.mobius.mbk' =>
        [
            0 => 'mbk',
        ],
        'application/mbox' =>
        [
            0 => 'mbox',
        ],
        'application/vnd.medcalcdata' =>
        [
            0 => 'mc1',
        ],
        'application/vnd.mcd' =>
        [
            0 => 'mcd',
        ],
        'text/vnd.curl.mcurl' =>
        [
            0 => 'mcurl',
        ],
        'application/x-msaccess' =>
        [
            0 => 'mdb',
        ],
        'image/vnd.ms-modi' =>
        [
            0 => 'mdi',
        ],
        'model/mesh' =>
        [
            0 => 'mesh',
            1 => 'msh',
            2 => 'silo',
        ],
        'application/metalink4+xml' =>
        [
            0 => 'meta4',
        ],
        'application/metalink+xml' =>
        [
            0 => 'metalink',
        ],
        'application/mets+xml' =>
        [
            0 => 'mets',
        ],
        'application/vnd.mfmp' =>
        [
            0 => 'mfm',
        ],
        'application/rpki-manifest' =>
        [
            0 => 'mft',
        ],
        'application/vnd.osgeo.mapguide.package' =>
        [
            0 => 'mgp',
        ],
        'application/vnd.proteus.magazine' =>
        [
            0 => 'mgz',
        ],
        'application/x-mie' =>
        [
            0 => 'mie',
        ],
        'application/vnd.mif' =>
        [
            0 => 'mif',
        ],
        'video/mj2' =>
        [
            0 => 'mj2',
            1 => 'mjp2',
        ],
        'video/x-matroska' =>
        [
            0 => 'mk3d',
            1 => 'mks',
            2 => 'mkv',
        ],
        'audio/x-matroska' =>
        [
            0 => 'mka',
        ],
        'application/vnd.dolby.mlp' =>
        [
            0 => 'mlp',
        ],
        'application/vnd.chipnuts.karaoke-mmd' =>
        [
            0 => 'mmd',
        ],
        'application/vnd.smaf' =>
        [
            0 => 'mmf',
        ],
        'image/vnd.fujixerox.edmics-mmr' =>
        [
            0 => 'mmr',
        ],
        'video/x-mng' =>
        [
            0 => 'mng',
        ],
        'application/x-msmoney' =>
        [
            0 => 'mny',
        ],
        'application/x-mobipocket-ebook' =>
        [
            0 => 'mobi',
            1 => 'prc',
        ],
        'application/mods+xml' =>
        [
            0 => 'mods',
        ],
        'video/quicktime' =>
        [
            0 => 'mov',
            1 => 'qt',
        ],
        'video/x-sgi-movie' =>
        [
            0 => 'movie',
        ],
        'video/mp4' =>
        [
            0 => 'mp4',
            1 => 'mp4v',
            2 => 'mpg4',
        ],
        'application/mp4' =>
        [
            0 => 'mp4s',
        ],
        'application/vnd.mophun.certificate' =>
        [
            0 => 'mpc',
        ],
        'application/vnd.apple.installer+xml' =>
        [
            0 => 'mpkg',
        ],
        'application/vnd.blueice.multipass' =>
        [
            0 => 'mpm',
        ],
        'application/vnd.mophun.application' =>
        [
            0 => 'mpn',
        ],
        'application/vnd.ms-project' =>
        [
            0 => 'mpp',
            1 => 'mpt',
        ],
        'application/vnd.ibm.minipay' =>
        [
            0 => 'mpy',
        ],
        'application/vnd.mobius.mqy' =>
        [
            0 => 'mqy',
        ],
        'application/marc' =>
        [
            0 => 'mrc',
        ],
        'application/marcxml+xml' =>
        [
            0 => 'mrcx',
        ],
        'application/mediaservercontrol+xml' =>
        [
            0 => 'mscml',
        ],
        'application/vnd.fdsn.mseed' =>
        [
            0 => 'mseed',
        ],
        'application/vnd.mseq' =>
        [
            0 => 'mseq',
        ],
        'application/vnd.epson.msf' =>
        [
            0 => 'msf',
        ],
        'application/vnd.mobius.msl' =>
        [
            0 => 'msl',
        ],
        'application/vnd.muvee.style' =>
        [
            0 => 'msty',
        ],
        'model/vnd.mts' =>
        [
            0 => 'mts',
        ],
        'application/vnd.musician' =>
        [
            0 => 'mus',
        ],
        'application/vnd.recordare.musicxml+xml' =>
        [
            0 => 'musicxml',
        ],
        'application/vnd.mfer' =>
        [
            0 => 'mwf',
        ],
        'application/mxf' =>
        [
            0 => 'mxf',
        ],
        'application/vnd.recordare.musicxml' =>
        [
            0 => 'mxl',
        ],
        'application/xv+xml' =>
        [
            0 => 'mxml',
            1 => 'xhvml',
            2 => 'xvm',
            3 => 'xvml',
        ],
        'application/vnd.triscape.mxs' =>
        [
            0 => 'mxs',
        ],
        'application/vnd.nokia.n-gage.symbian.install' =>
        [
            0 => 'n-gage',
        ],
        'text/n3' =>
        [
            0 => 'n3',
        ],
        'application/vnd.wolfram.player' =>
        [
            0 => 'nbp',
        ],
        'application/x-dtbncx+xml' =>
        [
            0 => 'ncx',
        ],
        'text/x-nfo' =>
        [
            0 => 'nfo',
        ],
        'application/vnd.nokia.n-gage.data' =>
        [
            0 => 'ngdat',
        ],
        'application/vnd.nitf' =>
        [
            0 => 'nitf',
            1 => 'ntf',
        ],
        'application/vnd.neurolanguage.nlu' =>
        [
            0 => 'nlu',
        ],
        'application/vnd.enliven' =>
        [
            0 => 'nml',
        ],
        'application/vnd.noblenet-directory' =>
        [
            0 => 'nnd',
        ],
        'application/vnd.noblenet-sealer' =>
        [
            0 => 'nns',
        ],
        'application/vnd.noblenet-web' =>
        [
            0 => 'nnw',
        ],
        'image/vnd.net-fpx' =>
        [
            0 => 'npx',
        ],
        'application/x-conference' =>
        [
            0 => 'nsc',
        ],
        'application/vnd.lotus-notes' =>
        [
            0 => 'nsf',
        ],
        'application/x-iwork-numbers-sffnumbers' =>
        [
            0 => 'numbers'
        ],
        'application/x-nzb' =>
        [
            0 => 'nzb',
        ],
        'application/vnd.fujitsu.oasys2' =>
        [
            0 => 'oa2',
        ],
        'application/vnd.fujitsu.oasys3' =>
        [
            0 => 'oa3',
        ],
        'application/vnd.fujitsu.oasys' =>
        [
            0 => 'oas',
        ],
        'application/x-msbinder' =>
        [
            0 => 'obd',
        ],
        'application/x-tgif' =>
        [
            0 => 'obj',
        ],
        'application/oda' =>
        [
            0 => 'oda',
        ],
        'application/vnd.oasis.opendocument.database' =>
        [
            0 => 'odb',
        ],
        'application/vnd.oasis.opendocument.chart' =>
        [
            0 => 'odc',
        ],
        'application/vnd.oasis.opendocument.formula' =>
        [
            0 => 'odf',
        ],
        'application/vnd.oasis.opendocument.formula-template' =>
        [
            0 => 'odft',
        ],
        'application/vnd.oasis.opendocument.graphics' =>
        [
            0 => 'odg',
        ],
        'application/vnd.oasis.opendocument.image' =>
        [
            0 => 'odi',
        ],
        'application/vnd.oasis.opendocument.text-master' =>
        [
            0 => 'odm',
        ],
        'application/vnd.oasis.opendocument.presentation' =>
        [
            0 => 'odp',
        ],
        'application/vnd.oasis.opendocument.spreadsheet' =>
        [
            0 => 'ods',
        ],
        'application/vnd.oasis.opendocument.text' =>
        [
            0 => 'odt',
        ],
        'audio/ogg' =>
        [
            0 => 'oga',
            1 => 'ogg',
            2 => 'spx',
        ],
        'video/ogg' =>
        [
            0 => 'ogv',
        ],
        'application/ogg' =>
        [
            0 => 'ogx',
        ],
        'application/omdoc+xml' =>
        [
            0 => 'omdoc',
        ],
        'application/onenote' =>
        [
            0 => 'onepkg',
            1 => 'onetmp',
            2 => 'onetoc',
            3 => 'onetoc2',
        ],
        'application/oebps-package+xml' =>
        [
            0 => 'opf',
        ],
        'text/x-opml' =>
        [
            0 => 'opml',
        ],
        'application/vnd.palm' =>
        [
            0 => 'oprc',
            1 => 'pdb',
            2 => 'pqa',
        ],
        'application/vnd.lotus-organizer' =>
        [
            0 => 'org',
        ],
        'application/vnd.yamaha.openscoreformat' =>
        [
            0 => 'osf',
        ],
        'application/vnd.yamaha.openscoreformat.osfpvg+xml' =>
        [
            0 => 'osfpvg',
        ],
        'application/vnd.oasis.opendocument.chart-template' =>
        [
            0 => 'otc',
        ],
        'font/otf' =>
        [
            0 => 'otf',
        ],
        'application/vnd.oasis.opendocument.graphics-template' =>
        [
            0 => 'otg',
        ],
        'application/vnd.oasis.opendocument.text-web' =>
        [
            0 => 'oth',
        ],
        'application/vnd.oasis.opendocument.image-template' =>
        [
            0 => 'oti',
        ],
        'application/vnd.oasis.opendocument.presentation-template' =>
        [
            0 => 'otp',
        ],
        'application/vnd.oasis.opendocument.spreadsheet-template' =>
        [
            0 => 'ots',
        ],
        'application/vnd.oasis.opendocument.text-template' =>
        [
            0 => 'ott',
        ],
        'application/oxps' =>
        [
            0 => 'oxps',
        ],
        'application/vnd.openofficeorg.extension' =>
        [
            0 => 'oxt',
        ],
        'text/x-pascal' =>
        [
            0 => 'p',
            1 => 'pas',
        ],
        'application/pkcs10' =>
        [
            0 => 'p10',
        ],
        'application/x-pkcs12' =>
        [
            0 => 'p12',
            1 => 'pfx',
        ],
        'application/x-pkcs7-certificates' =>
        [
            0 => 'p7b',
            1 => 'spc',
        ],
        'application/pkcs7-mime' =>
        [
            0 => 'p7c',
            1 => 'p7m',
        ],
        'application/x-pkcs7-certreqresp' =>
        [
            0 => 'p7r',
        ],
        'application/pkcs7-signature' =>
        [
            0 => 'p7s',
        ],
        'application/pkcs8' =>
        [
            0 => 'p8',
        ],
        'application/x-iwork-pages-sffpages' =>
        [
            0 => 'pages'
        ],
        'application/vnd.pawaafile' =>
        [
            0 => 'paw',
        ],
        'application/vnd.powerbuilder6' =>
        [
            0 => 'pbd',
        ],
        'image/x-portable-bitmap' =>
        [
            0 => 'pbm',
        ],
        'application/x-font-pcf' =>
        [
            0 => 'pcf',
        ],
        'application/vnd.hp-pcl' =>
        [
            0 => 'pcl',
        ],
        'application/vnd.hp-pclxl' =>
        [
            0 => 'pclxl',
        ],
        'image/x-pict' =>
        [
            0 => 'pct',
            1 => 'pic',
        ],
        'application/vnd.curl.pcurl' =>
        [
            0 => 'pcurl',
        ],
        'image/x-pcx' =>
        [
            0 => 'pcx',
        ],
        'application/pdf' =>
        [
            0 => 'pdf',
        ],
        'application/font-tdpfr' =>
        [
            0 => 'pfr',
        ],
        'image/x-portable-graymap' =>
        [
            0 => 'pgm',
        ],
        'application/x-chess-pgn' =>
        [
            0 => 'pgn',
        ],
        'application/pgp-encrypted' =>
        [
            0 => 'pgp',
        ],
        'application/pkixcmp' =>
        [
            0 => 'pki',
        ],
        'application/pkix-pkipath' =>
        [
            0 => 'pkipath',
        ],
        'application/vnd.3gpp.pic-bw-large' =>
        [
            0 => 'plb',
        ],
        'application/vnd.mobius.plc' =>
        [
            0 => 'plc',
        ],
        'application/vnd.pocketlearn' =>
        [
            0 => 'plf',
        ],
        'application/pls+xml' =>
        [
            0 => 'pls',
        ],
        'application/vnd.ctc-posml' =>
        [
            0 => 'pml',
        ],
        'image/png' =>
        [
            0 => 'png',
        ],
        'image/x-portable-anymap' =>
        [
            0 => 'pnm',
        ],
        'application/vnd.macports.portpkg' =>
        [
            0 => 'portpkg',
        ],
        'application/vnd.ms-powerpoint' =>
        [
            0 => 'pot',
            1 => 'pps',
            2 => 'ppt',
        ],
        'application/vnd.ms-powerpoint.template.macroenabled.12' =>
        [
            0 => 'potm',
        ],
        'application/vnd.openxmlformats-officedocument.presentationml.template' =>
        [
            0 => 'potx',
        ],
        'application/vnd.ms-powerpoint.addin.macroenabled.12' =>
        [
            0 => 'ppam',
        ],
        'application/vnd.cups-ppd' =>
        [
            0 => 'ppd',
        ],
        'image/x-portable-pixmap' =>
        [
            0 => 'ppm',
        ],
        'application/vnd.ms-powerpoint.slideshow.macroenabled.12' =>
        [
            0 => 'ppsm',
        ],
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow' =>
        [
            0 => 'ppsx',
        ],
        'application/vnd.ms-powerpoint.presentation.macroenabled.12' =>
        [
            0 => 'pptm',
        ],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' =>
        [
            0 => 'pptx',
        ],
        'application/vnd.lotus-freelance' =>
        [
            0 => 'pre',
        ],
        'application/pics-rules' =>
        [
            0 => 'prf',
        ],
        'application/vnd.3gpp.pic-bw-small' =>
        [
            0 => 'psb',
        ],
        'image/vnd.adobe.photoshop' =>
        [
            0 => 'psd',
        ],
        'application/x-font-linux-psf' =>
        [
            0 => 'psf',
        ],
        'application/pskc+xml' =>
        [
            0 => 'pskcxml',
        ],
        'application/vnd.pvi.ptid1' =>
        [
            0 => 'ptid',
        ],
        'application/x-mspublisher' =>
        [
            0 => 'pub',
        ],
        'application/vnd.3gpp.pic-bw-var' =>
        [
            0 => 'pvb',
        ],
        'application/vnd.3m.post-it-notes' =>
        [
            0 => 'pwn',
        ],
        'audio/vnd.ms-playready.media.pya' =>
        [
            0 => 'pya',
        ],
        'video/vnd.ms-playready.media.pyv' =>
        [
            0 => 'pyv',
        ],
        'application/vnd.epson.quickanime' =>
        [
            0 => 'qam',
        ],
        'application/vnd.intu.qbo' =>
        [
            0 => 'qbo',
        ],
        'application/vnd.intu.qfx' =>
        [
            0 => 'qfx',
        ],
        'application/vnd.publishare-delta-tree' =>
        [
            0 => 'qps',
        ],
        'application/vnd.quark.quarkxpress' =>
        [
            0 => 'qwd',
            1 => 'qwt',
            2 => 'qxb',
            3 => 'qxd',
            4 => 'qxl',
            5 => 'qxt',
        ],
        'audio/x-pn-realaudio' =>
        [
            0 => 'ra',
            1 => 'ram',
        ],
        'application/x-rar-compressed' =>
        [
            0 => 'rar',
        ],
        'image/x-cmu-raster' =>
        [
            0 => 'ras',
        ],
        'application/vnd.ipunplugged.rcprofile' =>
        [
            0 => 'rcprofile',
        ],
        'application/rdf+xml' =>
        [
            0 => 'rdf',
        ],
        'application/vnd.data-vision.rdz' =>
        [
            0 => 'rdz',
        ],
        'application/vnd.businessobjects' =>
        [
            0 => 'rep',
        ],
        'application/x-dtbresource+xml' =>
        [
            0 => 'res',
        ],
        'image/x-rgb' =>
        [
            0 => 'rgb',
        ],
        'application/reginfo+xml' =>
        [
            0 => 'rif',
        ],
        'audio/vnd.rip' =>
        [
            0 => 'rip',
        ],
        'application/x-research-info-systems' =>
        [
            0 => 'ris',
        ],
        'application/resource-lists+xml' =>
        [
            0 => 'rl',
        ],
        'image/vnd.fujixerox.edmics-rlc' =>
        [
            0 => 'rlc',
        ],
        'application/resource-lists-diff+xml' =>
        [
            0 => 'rld',
        ],
        'application/vnd.rn-realmedia' =>
        [
            0 => 'rm',
        ],
        'audio/x-pn-realaudio-plugin' =>
        [
            0 => 'rmp',
        ],
        'application/vnd.jcp.javame.midlet-rms' =>
        [
            0 => 'rms',
        ],
        'application/vnd.rn-realmedia-vbr' =>
        [
            0 => 'rmvb',
        ],
        'application/relax-ng-compact-syntax' =>
        [
            0 => 'rnc',
        ],
        'application/rpki-roa' =>
        [
            0 => 'roa',
        ],
        'application/vnd.cloanto.rp9' =>
        [
            0 => 'rp9',
        ],
        'application/vnd.nokia.radio-presets' =>
        [
            0 => 'rpss',
        ],
        'application/vnd.nokia.radio-preset' =>
        [
            0 => 'rpst',
        ],
        'application/sparql-query' =>
        [
            0 => 'rq',
        ],
        'application/rls-services+xml' =>
        [
            0 => 'rs',
        ],
        'application/rsd+xml' =>
        [
            0 => 'rsd',
        ],
        'application/rss+xml' =>
        [
            0 => 'rss',
        ],
        'application/rtf' =>
        [
            0 => 'rtf',
        ],
        'text/richtext' =>
        [
            0 => 'rtx',
        ],
        'audio/s3m' =>
        [
            0 => 's3m',
        ],
        'application/vnd.yamaha.smaf-audio' =>
        [
            0 => 'saf',
        ],
        'application/sbml+xml' =>
        [
            0 => 'sbml',
        ],
        'application/vnd.ibm.secure-container' =>
        [
            0 => 'sc',
        ],
        'application/x-msschedule' =>
        [
            0 => 'scd',
        ],
        'application/vnd.lotus-screencam' =>
        [
            0 => 'scm',
        ],
        'application/scvp-cv-request' =>
        [
            0 => 'scq',
        ],
        'application/scvp-cv-response' =>
        [
            0 => 'scs',
        ],
        'text/vnd.curl.scurl' =>
        [
            0 => 'scurl',
        ],
        'application/vnd.stardivision.draw' =>
        [
            0 => 'sda',
        ],
        'application/vnd.stardivision.calc' =>
        [
            0 => 'sdc',
        ],
        'application/vnd.stardivision.impress' =>
        [
            0 => 'sdd',
        ],
        'application/vnd.solent.sdkm+xml' =>
        [
            0 => 'sdkd',
            1 => 'sdkm',
        ],
        'application/sdp' =>
        [
            0 => 'sdp',
        ],
        'application/vnd.stardivision.writer' =>
        [
            0 => 'sdw',
            1 => 'vor',
        ],
        'application/vnd.seemail' =>
        [
            0 => 'see',
        ],
        'application/vnd.sema' =>
        [
            0 => 'sema',
        ],
        'application/vnd.semd' =>
        [
            0 => 'semd',
        ],
        'application/vnd.semf' =>
        [
            0 => 'semf',
        ],
        'application/java-serialized-object' =>
        [
            0 => 'ser',
        ],
        'application/set-payment-initiation' =>
        [
            0 => 'setpay',
        ],
        'application/set-registration-initiation' =>
        [
            0 => 'setreg',
        ],
        'application/vnd.hydrostatix.sof-data' =>
        [
            0 => 'sfd-hdstx',
        ],
        'application/vnd.spotfire.sfs' =>
        [
            0 => 'sfs',
        ],
        'text/x-sfv' =>
        [
            0 => 'sfv',
        ],
        'image/sgi' =>
        [
            0 => 'sgi',
        ],
        'application/vnd.stardivision.writer-global' =>
        [
            0 => 'sgl',
        ],
        'text/sgml' =>
        [
            0 => 'sgm',
            1 => 'sgml',
        ],
        'application/x-sh' =>
        [
            0 => 'sh',
        ],
        'application/x-shar' =>
        [
            0 => 'shar',
        ],
        'application/shf+xml' =>
        [
            0 => 'shf',
        ],
        'image/x-mrsid-image' =>
        [
            0 => 'sid',
        ],
        'audio/silk' =>
        [
            0 => 'sil',
        ],
        'application/vnd.symbian.install' =>
        [
            0 => 'sis',
            1 => 'sisx',
        ],
        'application/x-stuffit' =>
        [
            0 => 'sit',
        ],
        'application/x-stuffitx' =>
        [
            0 => 'sitx',
        ],
        'application/vnd.koan' =>
        [
            0 => 'skd',
            1 => 'skm',
            2 => 'skp',
            3 => 'skt',
        ],
        'application/vnd.ms-powerpoint.slide.macroenabled.12' =>
        [
            0 => 'sldm',
        ],
        'application/vnd.openxmlformats-officedocument.presentationml.slide' =>
        [
            0 => 'sldx',
        ],
        'application/vnd.epson.salt' =>
        [
            0 => 'slt',
        ],
        'application/vnd.stepmania.stepchart' =>
        [
            0 => 'sm',
        ],
        'application/vnd.stardivision.math' =>
        [
            0 => 'smf',
        ],
        'application/smil+xml' =>
        [
            0 => 'smi',
            1 => 'smil',
        ],
        'video/x-smv' =>
        [
            0 => 'smv',
        ],
        'application/vnd.stepmania.package' =>
        [
            0 => 'smzip',
        ],
        'application/x-font-snf' =>
        [
            0 => 'snf',
        ],
        'application/vnd.yamaha.smaf-phrase' =>
        [
            0 => 'spf',
        ],
        'application/x-futuresplash' =>
        [
            0 => 'spl',
        ],
        'text/vnd.in3d.spot' =>
        [
            0 => 'spot',
        ],
        'application/scvp-vp-response' =>
        [
            0 => 'spp',
        ],
        'application/scvp-vp-request' =>
        [
            0 => 'spq',
        ],
        'application/x-sql' =>
        [
            0 => 'sql',
        ],
        'application/x-wais-source' =>
        [
            0 => 'src',
        ],
        'application/x-subrip' =>
        [
            0 => 'srt',
        ],
        'application/sru+xml' =>
        [
            0 => 'sru',
        ],
        'application/sparql-results+xml' =>
        [
            0 => 'srx',
        ],
        'application/ssdl+xml' =>
        [
            0 => 'ssdl',
        ],
        'application/vnd.kodak-descriptor' =>
        [
            0 => 'sse',
        ],
        'application/vnd.epson.ssf' =>
        [
            0 => 'ssf',
        ],
        'application/ssml+xml' =>
        [
            0 => 'ssml',
        ],
        'application/vnd.sailingtracker.track' =>
        [
            0 => 'st',
        ],
        'application/vnd.sun.xml.calc.template' =>
        [
            0 => 'stc',
        ],
        'application/vnd.sun.xml.draw.template' =>
        [
            0 => 'std',
        ],
        'application/vnd.wt.stf' =>
        [
            0 => 'stf',
        ],
        'application/vnd.sun.xml.impress.template' =>
        [
            0 => 'sti',
        ],
        'application/hyperstudio' =>
        [
            0 => 'stk',
        ],
        'application/vnd.ms-pki.stl' =>
        [
            0 => 'stl',
        ],
        'application/vnd.pg.format' =>
        [
            0 => 'str',
        ],
        'application/vnd.sun.xml.writer.template' =>
        [
            0 => 'stw',
        ],
        'text/vnd.dvb.subtitle' =>
        [
            0 => 'sub',
        ],
        'image/vnd.dvb.subtitle' =>
        [
            0 => 'sub',
        ],
        'application/vnd.sus-calendar' =>
        [
            0 => 'sus',
            1 => 'susp',
        ],
        'application/x-sv4cpio' =>
        [
            0 => 'sv4cpio',
        ],
        'application/x-sv4crc' =>
        [
            0 => 'sv4crc',
        ],
        'application/vnd.dvb.service' =>
        [
            0 => 'svc',
        ],
        'application/vnd.svd' =>
        [
            0 => 'svd',
        ],
        'image/svg+xml' =>
        [
            0 => 'svg',
            1 => 'svgz',
        ],
        'application/x-shockwave-flash' =>
        [
            0 => 'swf',
        ],
        'application/vnd.aristanetworks.swi' =>
        [
            0 => 'swi',
        ],
        'application/vnd.sun.xml.calc' =>
        [
            0 => 'sxc',
        ],
        'application/vnd.sun.xml.draw' =>
        [
            0 => 'sxd',
        ],
        'application/vnd.sun.xml.writer.global' =>
        [
            0 => 'sxg',
        ],
        'application/vnd.sun.xml.impress' =>
        [
            0 => 'sxi',
        ],
        'application/vnd.sun.xml.math' =>
        [
            0 => 'sxm',
        ],
        'application/vnd.sun.xml.writer' =>
        [
            0 => 'sxw',
        ],
        'application/x-t3vm-image' =>
        [
            0 => 't3',
        ],
        'application/vnd.mynfc' =>
        [
            0 => 'taglet',
        ],
        'application/vnd.tao.intent-module-archive' =>
        [
            0 => 'tao',
        ],
        'application/x-tar' =>
        [
            0 => 'tar',
        ],
        'application/vnd.3gpp2.tcap' =>
        [
            0 => 'tcap',
        ],
        'application/x-tcl' =>
        [
            0 => 'tcl',
        ],
        'application/vnd.smart.teacher' =>
        [
            0 => 'teacher',
        ],
        'application/tei+xml' =>
        [
            0 => 'tei',
            1 => 'teicorpus',
        ],
        'application/x-tex' =>
        [
            0 => 'tex',
        ],
        'application/x-texinfo' =>
        [
            0 => 'texi',
            1 => 'texinfo',
        ],
        'application/thraud+xml' =>
        [
            0 => 'tfi',
        ],
        'application/x-tex-tfm' =>
        [
            0 => 'tfm',
        ],
        'image/x-tga' =>
        [
            0 => 'tga',
        ],
        'application/vnd.ms-officetheme' =>
        [
            0 => 'thmx',
        ],
        'image/tiff' =>
        [
            0 => 'tif',
            1 => 'tiff',
        ],
        'application/vnd.tmobile-livetv' =>
        [
            0 => 'tmo',
        ],
        'application/x-bittorrent' =>
        [
            0 => 'torrent',
        ],
        'application/vnd.groove-tool-template' =>
        [
            0 => 'tpl',
        ],
        'application/vnd.trid.tpt' =>
        [
            0 => 'tpt',
        ],
        'application/vnd.trueapp' =>
        [
            0 => 'tra',
        ],
        'application/x-msterminal' =>
        [
            0 => 'trm',
        ],
        'application/timestamped-data' =>
        [
            0 => 'tsd',
        ],
        'text/tab-separated-values' =>
        [
            0 => 'tsv',
        ],
        'font/collection' =>
        [
            0 => 'ttc',
        ],
        'font/ttf' =>
        [
            0 => 'ttf',
        ],
        'text/turtle' =>
        [
            0 => 'ttl',
        ],
        'application/vnd.simtech-mindmapper' =>
        [
            0 => 'twd',
            1 => 'twds',
        ],
        'application/vnd.genomatix.tuxedo' =>
        [
            0 => 'txd',
        ],
        'application/vnd.mobius.txf' =>
        [
            0 => 'txf',
        ],
        'application/vnd.ufdl' =>
        [
            0 => 'ufd',
            1 => 'ufdl',
        ],
        'application/x-glulx' =>
        [
            0 => 'ulx',
        ],
        'application/vnd.umajin' =>
        [
            0 => 'umj',
        ],
        'application/vnd.unity' =>
        [
            0 => 'unityweb',
        ],
        'application/vnd.uoml+xml' =>
        [
            0 => 'uoml',
        ],
        'text/uri-list' =>
        [
            0 => 'uri',
            1 => 'uris',
            2 => 'urls',
        ],
        'application/x-ustar' =>
        [
            0 => 'ustar',
        ],
        'application/vnd.uiq.theme' =>
        [
            0 => 'utz',
        ],
        'text/x-uuencode' =>
        [
            0 => 'uu',
        ],
        'audio/vnd.dece.audio' =>
        [
            0 => 'uva',
            1 => 'uvva',
        ],
        'application/vnd.dece.data' =>
        [
            0 => 'uvd',
            1 => 'uvf',
            2 => 'uvvd',
            3 => 'uvvf',
        ],
        'image/vnd.dece.graphic' =>
        [
            0 => 'uvg',
            1 => 'uvi',
            2 => 'uvvg',
            3 => 'uvvi',
        ],
        'video/vnd.dece.hd' =>
        [
            0 => 'uvh',
            1 => 'uvvh',
        ],
        'video/vnd.dece.mobile' =>
        [
            0 => 'uvm',
            1 => 'uvvm',
        ],
        'video/vnd.dece.pd' =>
        [
            0 => 'uvp',
            1 => 'uvvp',
        ],
        'video/vnd.dece.sd' =>
        [
            0 => 'uvs',
            1 => 'uvvs',
        ],
        'application/vnd.dece.ttml+xml' =>
        [
            0 => 'uvt',
            1 => 'uvvt',
        ],
        'video/vnd.uvvu.mp4' =>
        [
            0 => 'uvu',
            1 => 'uvvu',
        ],
        'video/vnd.dece.video' =>
        [
            0 => 'uvv',
            1 => 'uvvv',
        ],
        'application/vnd.dece.unspecified' =>
        [
            0 => 'uvvx',
            1 => 'uvx',
        ],
        'application/vnd.dece.zip' =>
        [
            0 => 'uvvz',
            1 => 'uvz',
        ],
        'text/vcard' =>
        [
            0 => 'vcard',
        ],
        'application/x-cdlink' =>
        [
            0 => 'vcd',
        ],
        'text/x-vcard' =>
        [
            0 => 'vcf',
        ],
        'application/vnd.groove-vcard' =>
        [
            0 => 'vcg',
        ],
        'text/x-vcalendar' =>
        [
            0 => 'vcs',
        ],
        'application/vnd.vcx' =>
        [
            0 => 'vcx',
        ],
        'application/vnd.visionary' =>
        [
            0 => 'vis',
        ],
        'video/vnd.vivo' =>
        [
            0 => 'viv',
        ],
        'video/x-ms-vob' =>
        [
            0 => 'vob',
        ],
        'model/vrml' =>
        [
            0 => 'vrml',
            1 => 'wrl',
        ],
        'application/vnd.visio' =>
        [
            0 => 'vsd',
            1 => 'vss',
            2 => 'vst',
            3 => 'vsw',
        ],
        'application/vnd.vsf' =>
        [
            0 => 'vsf',
        ],
        'model/vnd.vtu' =>
        [
            0 => 'vtu',
        ],
        'application/voicexml+xml' =>
        [
            0 => 'vxml',
        ],
        'application/x-doom' =>
        [
            0 => 'wad',
        ],
        'audio/wav' =>
        [
            0 => 'wav'
        ],
        'audio/x-wav' =>
        [
            0 => 'wav',
        ],
        'audio/x-ms-wax' =>
        [
            0 => 'wax',
        ],
        'image/vnd.wap.wbmp' =>
        [
            0 => 'wbmp',
        ],
        'application/vnd.criticaltools.wbs+xml' =>
        [
            0 => 'wbs',
        ],
        'application/vnd.wap.wbxml' =>
        [
            0 => 'wbxml',
        ],
        'application/vnd.ms-works' =>
        [
            0 => 'wcm',
            1 => 'wdb',
            2 => 'wks',
            3 => 'wps',
        ],
        'image/vnd.ms-photo' =>
        [
            0 => 'wdp',
        ],
        'audio/webm' =>
        [
            0 => 'weba',
        ],
        'video/webm' =>
        [
            0 => 'webm',
        ],
        'image/webp' =>
        [
            0 => 'webp',
        ],
        'application/vnd.pmi.widget' =>
        [
            0 => 'wg',
        ],
        'application/widget' =>
        [
            0 => 'wgt',
        ],
        'video/x-ms-wm' =>
        [
            0 => 'wm',
        ],
        'audio/x-ms-wma' =>
        [
            0 => 'wma',
        ],
        'application/x-ms-wmd' =>
        [
            0 => 'wmd',
        ],
        'text/vnd.wap.wml' =>
        [
            0 => 'wml',
        ],
        'application/vnd.wap.wmlc' =>
        [
            0 => 'wmlc',
        ],
        'text/vnd.wap.wmlscript' =>
        [
            0 => 'wmls',
        ],
        'application/vnd.wap.wmlscriptc' =>
        [
            0 => 'wmlsc',
        ],
        'video/x-ms-wmv' =>
        [
            0 => 'wmv',
        ],
        'video/x-ms-wmx' =>
        [
            0 => 'wmx',
        ],
        'font/woff' =>
        [
            0 => 'woff',
        ],
        'font/woff2' =>
        [
            0 => 'woff2',
        ],
        'application/vnd.wordperfect' =>
        [
            0 => 'wpd',
        ],
        'application/vnd.ms-wpl' =>
        [
            0 => 'wpl',
        ],
        'application/vnd.wqd' =>
        [
            0 => 'wqd',
        ],
        'application/x-mswrite' =>
        [
            0 => 'wri',
        ],
        'application/wsdl+xml' =>
        [
            0 => 'wsdl',
        ],
        'application/wspolicy+xml' =>
        [
            0 => 'wspolicy',
        ],
        'application/vnd.webturbo' =>
        [
            0 => 'wtb',
        ],
        'video/x-ms-wvx' =>
        [
            0 => 'wvx',
        ],
        'model/x3d+xml' =>
        [
            0 => 'x3d',
            1 => 'x3dz',
        ],
        'model/x3d+binary' =>
        [
            0 => 'x3db',
            1 => 'x3dbz',
        ],
        'model/x3d+vrml' =>
        [
            0 => 'x3dv',
            1 => 'x3dvz',
        ],
        'application/xaml+xml' =>
        [
            0 => 'xaml',
        ],
        'application/x-silverlight-app' =>
        [
            0 => 'xap',
        ],
        'application/vnd.xara' =>
        [
            0 => 'xar',
        ],
        'application/x-ms-xbap' =>
        [
            0 => 'xbap',
        ],
        'application/vnd.fujixerox.docuworks.binder' =>
        [
            0 => 'xbd',
        ],
        'image/x-xbitmap' =>
        [
            0 => 'xbm',
        ],
        'application/xcap-diff+xml' =>
        [
            0 => 'xdf',
        ],
        'application/vnd.syncml.dm+xml' =>
        [
            0 => 'xdm',
        ],
        'application/vnd.adobe.xdp+xml' =>
        [
            0 => 'xdp',
        ],
        'application/dssc+xml' =>
        [
            0 => 'xdssc',
        ],
        'application/vnd.fujixerox.docuworks' =>
        [
            0 => 'xdw',
        ],
        'application/xenc+xml' =>
        [
            0 => 'xenc',
        ],
        'application/patch-ops-error+xml' =>
        [
            0 => 'xer',
        ],
        'application/vnd.adobe.xfdf' =>
        [
            0 => 'xfdf',
        ],
        'application/vnd.xfdl' =>
        [
            0 => 'xfdl',
        ],
        'application/xhtml+xml' =>
        [
            0 => 'xht',
            1 => 'xhtml',
        ],
        'image/vnd.xiff' =>
        [
            0 => 'xif',
        ],
        'application/vnd.ms-excel' =>
        [
            0 => 'xla',
            1 => 'xlc',
            2 => 'xlm',
            3 => 'xls',
            4 => 'xlt',
            5 => 'xlw',
        ],
        'application/vnd.ms-excel.addin.macroenabled.12' =>
        [
            0 => 'xlam',
        ],
        'application/x-xliff+xml' =>
        [
            0 => 'xlf',
        ],
        'application/vnd.ms-excel.sheet.binary.macroenabled.12' =>
        [
            0 => 'xlsb',
        ],
        'application/vnd.ms-excel.sheet.macroenabled.12' =>
        [
            0 => 'xlsm',
        ],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' =>
        [
            0 => 'xlsx',
        ],
        'application/vnd.ms-excel.template.macroenabled.12' =>
        [
            0 => 'xltm',
        ],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template' =>
        [
            0 => 'xltx',
        ],
        'audio/xm' =>
        [
            0 => 'xm',
        ],
        'application/xml' =>
        [
            0 => 'xml',
            1 => 'xsl',
        ],
        'application/vnd.olpc-sugar' =>
        [
            0 => 'xo',
        ],
        'application/xop+xml' =>
        [
            0 => 'xop',
        ],
        'application/x-xpinstall' =>
        [
            0 => 'xpi',
        ],
        'application/xproc+xml' =>
        [
            0 => 'xpl',
        ],
        'image/x-xpixmap' =>
        [
            0 => 'xpm',
        ],
        'application/vnd.is-xpr' =>
        [
            0 => 'xpr',
        ],
        'application/vnd.ms-xpsdocument' =>
        [
            0 => 'xps',
        ],
        'application/vnd.intercon.formnet' =>
        [
            0 => 'xpw',
            1 => 'xpx',
        ],
        'application/xslt+xml' =>
        [
            0 => 'xslt',
        ],
        'application/vnd.syncml+xml' =>
        [
            0 => 'xsm',
        ],
        'application/xspf+xml' =>
        [
            0 => 'xspf',
        ],
        'application/vnd.mozilla.xul+xml' =>
        [
            0 => 'xul',
        ],
        'image/x-xwindowdump' =>
        [
            0 => 'xwd',
        ],
        'chemical/x-xyz' =>
        [
            0 => 'xyz',
        ],
        'application/x-xz' =>
        [
            0 => 'xz',
        ],
        'application/yang' =>
        [
            0 => 'yang',
        ],
        'application/yin+xml' =>
        [
            0 => 'yin',
        ],
        'application/x-zmachine' =>
        [
            0 => 'z1',
            1 => 'z2',
            2 => 'z3',
            3 => 'z4',
            4 => 'z5',
            5 => 'z6',
            6 => 'z7',
            7 => 'z8',
        ],
        'application/vnd.zzazz.deck+xml' =>
        [
            0 => 'zaz',
        ],
        'application/zip' =>
        [
            0 => 'zip',
        ],
        'application/vnd.zul' =>
        [
            0 => 'zir',
            1 => 'zirz',
        ],
        'application/vnd.handheld-entertainment+xml' =>
        [
            0 => 'zmm',
        ]
    ];

    /**
     * Private constructor.
     */
    private function __construct() {}
    
    /**
     * Generates a new map which is a subset of the MIME type to file extensions map.
     * 
     * @param array $MIMETypes Array of strings, each representing a MIME type.
     * @return array The subset map.
     */
    public static function generateMIMETypeFileExtensionsSubsetMap($MIMETypes) {
        $ret = [];
        foreach ($MIMETypes as $MIMEType) {
            if (isset(static::MIME_TYPE_FILE_EXTENSIONS_MAP[$MIMEType])) {
                $ret[$MIMEType] = static::MIME_TYPE_FILE_EXTENSIONS_MAP[$MIMEType];
            }
        }
        return $ret;
    }
    
}
