   M S B A M L   `   `   `  ���� J  FCL_CHRONO_USERS, Version=1.0.0.0, Culture=neutral, PublicKeyToken=null%clr-namespace:CL_CHRONO	CL_CHRONO      CL_CHRONO.Vista.Registro   W SPresentationCore, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35\ XPresentationFramework, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35e aSystem.Windows.Controls.Ribbon, Version=8.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089R NWindowsBase, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35^ ZWindowsFormsIntegration, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35H 9http://schemas.microsoft.com/winfx/2006/xaml/presentation      R NSystem.Xaml, Version=8.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089:x,http://schemas.microsoft.com/winfx/2006/xaml     @d2http://schemas.microsoft.com/expression/blend/2008     Jmc;http://schemas.openxmlformats.org/markup-compatibility/2006     )localclr-namespace:CL_CHRONO     -     � Icon$  /Resources/icon.ico�� � Title$ Registro��$	��500��$	��400�� � 	Resources    BotonColorAzul �� Color  TextoBotonColorBlanco  EstiloTextBoxConHint �� 
TargetType �� Property �� Value �� 
TargetType ��Name	 �� CornerRadius
 J� SnapsToDevicePixels ��Name ��Name J� Opacity �� RelativeSource �� AncestorType �� Triggers P� Property P� Value �� 
TargetName  	btnVolver �� Orientation �� Triggers �� Angle  EstiloBotonRegistro �� ScaleX �� ScaleY��%l  &	        &	      &	 &     &	 h    &	 P    �� $ #253439���� $ #FFFFFF���� " ��.���  � ��$ Center=���  � ��$ Center=���  � ��$ 20(���  � ��$
 Bold"���  � ��
 ���� ��  � �� �#253439��  � ��$ 2q���  � �� �� " ��.R��� $ border��#��z��#��z��#��z��$	 5��$

 True��._�� .+��� $ PART_ContentHost��$|�False��$��Hidden=�$��Hidden=��� $ placeholderTextBlock��$��5,0,0,0q�$��Center=�$��16(�$��Regular"����#A0A0A0$y�	Collapsed=�$	 0.7Q����� *Tag+ ��  TextBox P�  � ��$  ��. ��� $ placeholderTextBlock�� � y�$ Visible=�P�  �@J�	IsFocused$
 True��. ��� $ placeholderTextBlock�� � ��$ 10(��� $ placeholderTextBlock�� � ��$
 bold"��� $ placeholderTextBlock�� � ��$	 Top=��� $ placeholderTextBlock�� � ��$ 	5,-15,0,0q��� " ��.���  � ��
 ���� ��  � ��$ 0q���  �@J�RenderTransformOrigin$ 0.5,0.5&���  � �� �� " ��.R��� $ 
Horizontal=�$��Center=�$��Center=�.+��� $��/Resources/return.png��$��45��$��50��$��Uniform=��� $��Center=�$��Center=� P�  �@J�IsMouseOver$
 True��. ���  � z� �� $ 15Q���  �@J�Opacity$	 0.8Q�P�  �@J�IsMouseOver$ False��. ���  � z� �� $ 0Q���  �@J�Opacity$ 1Q��� " ��.���  � ��# �   ��  � ��# �  ��  � ��$ Segoe UI)���  � ��$ 32(���  � ��$
 Bold"���  � ��$ 0q���  � ��$ Center=���  � ��$ Center=���  � ��$ 0,0,0,0q���  �@J�RenderTransformOrigin$ 0.5,0.5&���  � �� �� " ��.R��� $ 	btnBorder��#��z��$	 8��$

 True��._��� #��z��#��z�� P�  �@J�IsMouseOver$
 True��. ���  � �� �#345A6E��  � z� �� $
 1.05Q�$
 1.05Q�.���  � RowDefinitions �� $
��0.3* ��� $
��0.3* ��� $
��0.3* ��� $
��0.3* ��� $
��0.3* ��� $
��0.3* �.+��� -    ��Name$ 	btnVolver��$��0��$
��Left=�$��50��$��50��$��
10,10,0,21q�#���  �� $��0��$��Center=�$��/Resources/LogoColor.png��$��Uniform=��� -    ��Name$ txtDni��$��1��$	��300��$��40��$��0,10,0,0q�$��Center=�$��Center=�#��[ 
 � Tag$ DNI(sin letra)�� �� 	MaxLength$ 8���� -   $ 	txtCodigo��$��2��$	��300��$��40��$��0,10,0,0q�$��Center=�$��Center=�#��[ $ Clave de acceso��$ 4���� -   $ 	txtNombre��$��3��$	��300��$��40��$��0,10,0,0q�$��Center=�$��Center=�#��[ $ Nombre���� -   $ txtApellidos��$��4��$	��300��$��40��$��0,10,0,0q�$��Center=�$��Center=�#��[ $ 	Apellidos���� -   $ btnRegistrarse��$��5��$	��300��$��40��$��Center=�$��Center=�$��18(�#���  $��Registrarse��