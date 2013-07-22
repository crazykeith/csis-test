' Begin retrieval of MAC Address
Dim objFSO, objFolder, objShell, objTextFile, objFile
Dim strDirectory, strFile, strText, strMAC

' We're interested in MAC addresses of physical adapters only
strQuery = "SELECT * FROM Win32_NetworkAdapter WHERE NetConnectionID > ''"

Set objWMIService = GetObject( "winmgmts://./root/CIMV2" )
Set colItems      = objWMIService.ExecQuery( strQuery, "WQL", 48 )

For Each objItem In colItems
    If InStr( strMAC, objItem.MACAddress ) = 0 Then
        strMAC   =  objItem.MACAddress
    End If
Next

strMAC = Replace(strMAC, ":", "")
' End retrieval of MAC Address



' Begin retrieval of Computer Name
Set WshNetwork = WScript.CreateObject("WScript.Network")
strName = WshNetwork.ComputerName
' End retrieval of Computer Name



' Begin appending of text document
strDirectory = "\\at-dorado\CS_Names\"
strFile = "CS_Names.txt"
strText = strMAC & " = " & WshNetwork.ComputerName

' Create the File System Object
Set objFSO = CreateObject("Scripting.FileSystemObject")

' Check that the strDirectory folder exists
If objFSO.FolderExists(strDirectory) Then
   Set objFolder = objFSO.GetFolder(strDirectory)
Else
   Set objFolder = objFSO.CreateFolder(strDirectory)
   WScript.Echo "Just created " & strDirectory
End If

If objFSO.FileExists(strDirectory & strFile) Then
  ' Set objFolder = objFSO.GetFolder(strDirectory)
   Set objFile = objFSO.OpenTextFile(strDirectory & strFile, ForReading)
Else
   Set objFile = objFSO.CreateTextFile(strDirectory & strFile)
   Set objFile = objFSO.OpenTextFile(strDirectory & strFile, ForReading)
   Wscript.Echo "Just created " & strDirectory & strFile
End If

Const ForReading = 1

Dim arrFileLines()
i = 0
Do Until objFile.AtEndOfStream
Redim Preserve arrFileLines(i)
arrFileLines(i) = objFile.ReadLine
i = i + 1
Loop
objFile.Close

set objFile = nothing
set objFolder = nothing
x = 0

For Each strLine in arrFileLines
  If strLine = strText Then
		x = 1
	End If
Next

If x = 0 Then
	' OpenTextFile Method needs a Const value
	' ForAppending = 8 ForReading = 1, ForWriting = 2
	Const ForAppending = 8

	Set objFile = objFSO.OpenTextFile(strDirectory & strFile, ForAppending, True)

	' Writes strText every time you run this VBScript
	objFile.WriteLine(strText)
	objFile.Close	
End If

WScript.Quit
' End appending of text document
