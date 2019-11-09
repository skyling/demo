<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        body {
            font-family: Arial;
        }

        #textarea {
            width: 400px;
            min-height: 20px;
            max-height: 300px;
            _height: 120px;
            margin-left: auto;
            margin-right: auto;
            padding: 3px;
            outline: 0;
            border: 1px solid #a0b3d6;
            font-size: 12px;
            line-height: 24px;
            padding: 2px;
            word-wrap: break-word;
            overflow-x: hidden;
            overflow-y: auto;
            border-color: rgba(82, 168, 236, 0.8);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6);
        }
    </style>
</head>

<body>
<input placeholder="秘密" style="border:none" value="{{ $data['pageheader'] }}"/>
<hr style="height:1px;border:none;border-top:1px solid #f0f0f0;"/>
<input placeholder="填写标题" style="width:100%;border:none;color:red;height:100px;font-size:40px;text-align:center"
       value="{{ $data['title'] }}"/>
<br>
<br>
<br>
<br>
<div style="text-align:center"><input placeholder="副标题" style="width:100%;border:none;text-align:center"
                                      value="{{ $data['subtitle'] }}"/></div>
<hr style="height:1px;border:none;border-top:1px solid #000000;"/>

<div style="width:50%;float:left">
    <table frame=rhs style="width:100%">
        <tr>
            <td style="width:100%">
                <div class="textarea" style="width:100%;font-size:16px;text-align:center"
                     contenteditable="true">{{ $data['contenttitle'] }}</div>
                <br>
                <br>
                <div class="textarea" style="width:100%;height: 550px;"
                     contenteditable="true">{!! $data['content'] !!}</div>
            </td>
        </tr>
    </table>
</div>
<div style="width:50%;float:left">
    <table style="width:100%">
        <tr>
            <td style="width:100%">
                <div style="color:red;text-align:center;font-weight:bold;margin-top:17px">批&nbsp&nbsp&nbsp&nbsp&nbsp示
                </div>
                <br>
                <br>
                <br>
                <div class="textarea" style="width:100%;height: 550px;" contenteditable="true"></div>
            </td>
        </tr>
    </table>
</div>

<hr style="width:100%;height:1px;border:none;border-top:1px solid #000000;"/>
<div style="float:left">承办人：<input placeholder="填写承办人姓名" style="border:none"/></div>
<div style="float:right">电话：<input placeholder="填写电话号码" style="border:none"/></div>
</body>
</html>