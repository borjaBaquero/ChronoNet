   M S B A M L   `   `   `  ���� J  FCL_CHRONO_USERS, Version=1.0.0.0, Culture=neutral, PublicKeyToken=null%clr-namespace:CL_CHRONO	CL_CHRONO  O Kmscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e0891&clr-namespace:System;assembly=mscorlibSystem &     CL_CHRONO.Vista.ListadoRegistros   W SPresentationCore, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35\ XPresentationFramework, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35e aSystem.Windows.Controls.Ribbon, Version=8.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089R NWindowsBase, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35^ ZWindowsFormsIntegration, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35H 9http://schemas.microsoft.com/winfx/2006/xaml/presentation      R NSystem.Xaml, Version=8.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089:x,http://schemas.microsoft.com/winfx/2006/xaml     @d2http://schemas.microsoft.com/expression/blend/2008     )localclr-namespace:CL_CHRONO     Jmc;http://schemas.openxmlformats.org/markup-compatibility/2006     6sys&clr-namespace:System;assembly=mscorlib     -     � Title$  ListadoRegistros��$	��700��$	��400��
������� � Icon$ /Resources/icon.ico�� � 
ResizeMode$ CanMinimize=� � 	Resources    CenteredTextStyle �� 
TargetType �� Property �� Value  dniNumeros.text  contrasena.text  codigo.text  nombre.text  apellidos.text  BotonColorAzul �� Color  TextoBotonColorBlanco  EstiloBotonAceptar �� 
TargetType	 ��Name
 �� CornerRadius J� SnapsToDevicePixels ��Name �� Triggers P� Property P� Value �� ScaleX �� ScaleY 	 EstiloBotonFiltros �� Padding ��Name 
 StyledButton  StyledButtonCancel��%L	  &	        &	 i     &	 �     &	 �     &	 �     &	 �     &	 �     &	 �     &	 �     &		     '�� �    'G� �    &	
 �    &	 �    �� " ��.���  � ��$ Center=���  � ��$ Center=���  �@��TextAlignment$ Center=���DNI sin letra����Password����Codigo Acceso����
Nombre����	Apellidos���� $ #253439 ���� $ #FFFFFF���� " ��.���  � ��# �  ��  � ��# �  ��  � ��$ Segoe UI)���  � ��$ 26(���  � ��$
 Bold"���  � ��$ 0q���  � ��$ Center=���  � ��$ Center=���  � ��$ 0,0,0,0q���  �@J�RenderTransformOrigin$ 0.5,0.5&���  � �� �� " ��.R��� $	 	btnBorder��#��z��#��z��$
 8��$
 True��._��� $ ContentPresenter��#��z��#��z��#��z��$|�False��# z  P�  �@J�IsMouseOver$
 True��. ���  � �� �#345A6E��  � z� �� $
 1.05Q�$
 1.05Q��� " ��.���  � ��# �  ��  � ��# �  ��  � ��$ Segoe UI)���  � ��$ 50(���  � ��$
 Bold"���  � ��$ 0q���  � ��$ Center=���  � ��$ Center=���  � ��$ 20,10,20,10q���  � �� �� " ��.R��� $	 	btnBorder��# z��#��z��#��z��$
 8��$
 True��._��� $ ContentPresenter��#��z��#��z��#��z��$|�False��# z �� " ��.���  � ��$	 200����  � ��$ 30����  � ��$ 0,10,0,0q���  � ��$ 5q���  � ��$ 1q���  � �� �#888��  � ��$	 Top=���  � ��$
 Left=���  � �� �� " ��.R��� #��z��#��z��#��z��$
 True��._��� $ PART_ContentHost���� " G�.���  � ��$	 200����  � ��$ 30����  � ��$ 0,10,0,0q���  � ��$ 5q���  � ��$ 1q���  � �� �#888��  � ��$	 Top=���  � ��$
 Left=���  � �� �� " G�.R��� #��z��#��z��#��z��$
 True��._��� $ PART_ContentHost���� " ��.���  � �� �#253439��  � ��
 �������  � �� �#253439��  � ��$ 1q���  � ��$	 5,2q���  � �� �� " ��.R��� #��z��#��z��#��z��$
 5��._��� $��Center=�$��Center=��� " ��.���  � ��
 �������  � ��
 �������  � ��
 �������  � ��$ 1q���  � ��$	 5,2q���  � �� �� " ��.R��� #��z��#��z��#��z��$
 5��._��� $��Center=�$��Center=�.��� .+�� $��	0,0,0,100q�.+�&   System.Windows.Controls.DataGrid  -   $��dataGrid��$��Stretch=� �� AlternationCount$ 2��   AutoGenerateColumns$ False�����#CCCCCC   ColumnWidth5  /System.Windows.Controls.DataGridLengthConverter$ * "   VerticalScrollBarVisibility$
 Auto=�   	RowHeight$ 40Q�$��16(�   HorizontalGridLinesBrush �#E0E0E0   VerticalGridLinesBrush �#E0E0E0   GridLinesVisibility$ 
Horizontal=�   	CellStyle �� *  $System.Windows.Controls.DataGridCell"  .���  � �� �#CCCCCC��  � ��$ 0,0,0,1q���  � ��$ 5q���  � ��
 �������  � ��$ 18(� P�  �@J�IsMouseOver$
 True��. ���  � �� �#F1F1F1   ColumnHeaderStyle �� =  7System.Windows.Controls.Primitives.DataGridColumnHeader"  .���  � ��$ 16(���  � ��$
 Bold"���  � �� �#253439��  � ��
 �������  � ��$
 10,5q���  � ��
 �   ���  � ��$ 0,0,0,1q�   RowStyle �� )  #System.Windows.Controls.DataGridRow"  .���  � ��
 ����� P�  �@ AlternationIndex$ 1��. ���  � ��$
 10,5q���  � �� �#F9F9F9��  � �� �#333333��  � ��
 �   ���  � ��$ 0,0,0,1q�   Columns 0  *System.Windows.Controls.DataGridTextColumn  ,  &System.Windows.Controls.DataGridColumn    Width$  82 !   Header$! Nombre��1  +System.Windows.Controls.DataGridBoundColumn"   Binding" �� *Nombre+  $  * $! 	Apellidos��" �� *	Apellidos+  $  90 $
! Tipo��" �� *Tipo+  $	  150 $! 	FechaHora��" �� *	FechaHora+  $	  100 #   ElementStyle## [  $! 
Incidencia��" �� *
Incidencia+  $  70 $! Horas��" �� *HorasTrabajadasFormato+� -   $ �Name$$ popupFiltros��$��False��$	��Top=�% � 	StaysOpen$% False��& � PlacementTarget& �� ' �� ElementName$' 
btnFiltros��.���� 
����������#333$��2q�$
 5��._��� $��10q�.+��� $��0,0,10,0q�( �� Orientation$( 
Horizontal=�.+��� $��0,0,10,0q�$��Center=�$��Fecha Inicio:��(	  "System.Windows.Controls.DatePicker	  -   ) 	 Name$) datePickerFechaInicio��$	��200��$��30���� $��0,0,0,10q�$( 
Horizontal=�.+��� $��0,0,10,0q�$��Center=�$��
Fecha Fin:��	  -   $) datePickerFechaFin��$	��200��$��30���� $��0,0,0,10q�$( 
Horizontal=�.+��� $��0,0,10,0q�$��Center=�$��Nombre:���� -   * ��Name$* cmbUsuarioNombreCompleto��$	��200��$��1��+ �� DisplayMemberPath$+ Nombre���� $��0,0,0,10q�$( 
Horizontal=�.+��� $��0,0,10,0q�$��Center=�$��Incidencia:���� -   $* cmbFiltroIncidencia��$	��200��$��30��.@��� .��Todos�� .��SI�� .��NO�� -   , ��Name$, btnAplicarFiltros��$	��150��$��40��$��APLICAR��$��20(��� -	   $, btnResetFiltros��$	��150��$��40��$��RESET��$��20(�� $��0,0,0,15q�$��Bottom=�- � ColumnDefinitions- �� $
��320* ��� $	��33* ��� $	��27* ��� $
��321* �.+��� -
   . ��Name$. txtReloj��$	��168��$��21,6,0,0q�$
��Left=�$	��Top=����#253439$��24(�$
��Bold"�
�������/ �� TextAlignment$/ Center=��� -   $, btnSalirPantalla��$��3��$	��151��$��68��$��
0,-22,10,0q�$��Right=�$	��Top=�$��VOLVER��$��12(�0 J� RenderTransformOrigin$0 0.656,0.544&�#���  �� -   $, 
btnFiltros��$��1��$	��151��$��68��$��0,-22,219,0q�$��Right=�$	��Top=�$��FILTROS��$��12(�#���  $��3��� -   $$ popupFormulario��$��False��$��Center=�$% False��.��� .+��� ���	#AA000000�� $	��300��$ 20q�$��Center=�$��Center=�
����������#253439$��2q�$
 10��._��� .+��� $��0,0,0,5q�$��Selecciona un usuario:���� -   $* comboUsuarios��$��0,0,0,10q��� $��0,0,0,5q�$��Fecha:��	  -   $) 
datePicker��$��0,0,0,10q��� $��0,0,0,5q�$��Hora:���� -   1 ��Name$1 txtHora��$	��120��$��0,0,0,10q�(
   "CL_CHRONO.TextBoxWatermarkBehavior2 
  	Watermark$2 hh:mm:ss���� $��0,0,0,5q�$��Incidencia:���� -   3 ��Name$3 checkIncidencia��$��0,0,0,10q�$��Si���� $��0,0,0,5q�$��Tipo:���� -   $* 	comboTipo��$��0,0,0,10q�.@��� $��Entrada��
4 � Tag$4 0���� $��Salida��$4 1���� $��Pausa��$4 2���� $��Reanudar��$4 3���� $��Right=�$( 
Horizontal=�.+��� -   $��5q�$	��5,2q�$��Guardar��#��[
 �� -   $��5q�$	��5,2q�$��Cancelar��#��[ 