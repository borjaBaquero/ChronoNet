   M S B A M L   `   `   `  ����J  FCL CHRONO USERS, Version=1.0.0.0, Culture=neutral, PublicKeyToken=null%clr-namespace:CL_CHRONO	CL_CHRONO  5   	       CL_CHRONO.Vista.Registro   5   	   W SPresentationCore, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35\ XPresentationFramework, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35e aSystem.Windows.Controls.Ribbon, Version=8.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089R NWindowsBase, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35^ ZWindowsFormsIntegration, Version=8.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35H 9http://schemas.microsoft.com/winfx/2006/xaml/presentation      5   	   R NSystem.Xaml, Version=8.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089:x,http://schemas.microsoft.com/winfx/2006/xaml     5   	   @d2http://schemas.microsoft.com/expression/blend/2008     5   	   Jmc;http://schemas.openxmlformats.org/markup-compatibility/2006     5   	   )localclr-namespace:CL_CHRONO     5   	   -   5          � Icon$  /Resources/icon.ico��5	   	    � Title$ Registro��5
   	   $	��500��6   $	��400��6'    � 	Resources 5         BotonColorAzul �� Color  TextoBotonColorBlanco  EstiloTextBoxConHint �� 
TargetType �� Property �� Value �� 
TargetType ��Name	 �� CornerRadius
 J� SnapsToDevicePixels ��Name ��Name J� Opacity �� RelativeSource �� AncestorType �� Triggers P� Property P� Value �� 
TargetName  	btnVolver �� Orientation �� Triggers �� Angle  EstiloBotonRegistro �� ScaleX �� ScaleY��%�  &	        &	 !     &	 B     &	 �    &	 �    �� 5   
   $ #253439��61   �� 5   
   $ #FFFFFF��68   �� 5   
   " ��6-   .�5      ��  � ��$ Center=�62   �� 5       � ��$ Center=�69   �� 5       � ��$ 20(�6)   �� 5       � ��$
 Bold"�6+   �� 5       � ��
 ���� 6+   �� 5       � �� �#2534396,   �� 5       � ��$ 2q�60   �� 5       � �� 5      �� 5      " ��6&   .R�5      �� $ border��5      #��z��5      #��z��5       #��z��5!      $	 5��5"      $

 True��5#      ._�5$      � .+�5%   "   �� $ PART_ContentHost��5&      $|�False��5'      $��Hidden=�5(      $��Hidden=�5)      �� 5*   "   $ placeholderTextBlock��5+      $��5,0,0,0q�5,      $��Center=�5-      $��16(�5.      $��Regular"�5/      ���#A0A0A0$y�	Collapsed=�52      $	 0.7Q�53      ��51      �� *Tag+ ��  TextBox53      54      55       56      P� 57       � ��$  ��66   . �58   "   �� $ placeholderTextBlock��6)    � y�$ Visible=�6a   59      P� 5:       �@J�	IsFocused$
 True��6;   . �5;   "   �� $ placeholderTextBlock��6)    � ��$ 10(�6_   �� 5<   "   $ placeholderTextBlock��6)    � ��$
 bold"�6a   �� 5=   "   $ placeholderTextBlock��6)    � ��$	 Top=�6h   �� 5>   "   $ placeholderTextBlock��6)    � ��$ 	5,-15,0,0q�6]   5?      5A      5C      5D      �� 5H   
   " ��6"   .�5I      ��  � ��
 ���� 6+   �� 5J       � ��$ 0q�60   �� 5K       �@J�RenderTransformOrigin$ 0.5,0.5&�66   �� 5L       � �� 5M      �� 5N      " ��6&   .R�5O      �� $ 
Horizontal=�6%   $��Center=�6>   $��Center=�6[   .+�5P      �� $��/Resources/return.png��6$   $��45��6C   $��50��6N   $��Uniform=�6Z   �� 5Q      $��Center=�6/   $��Center=�6L   5R      5S      5U       5V      P� 5W       �@J�IsMouseOver$
 True��61   . �5X      ��  � z� 5Y      �� 5Z      $ 15Q�6.   5\      �� 5]       �@J�Opacity$	 0.8Q�60   5^      P� 5_       �@J�IsMouseOver$ False��61   . �5`      ��  � z� 5a      �� 5b      $ 0Q�6.   5d      �� 5e       �@J�Opacity$ 1Q�60   5f      5h      �� 5j   
   " ��6,   .�5k      ��  � ��# �   6+   �� 5l       � ��# �  6+   �� 5m       � ��$ Segoe UI)�6+   �� 5n       � ��$ 32(�6)   �� 5o       � ��$
 Bold"�6+   �� 5p       � ��$ 0q�60   �� 5q       � ��$ Center=�6;   �� 5r       � ��$ Center=�69   �� 5s       � ��$ 0,0,0,0q�6(   �� 5t       �@J�RenderTransformOrigin$ 0.5,0.5&�66   �� 5u       � �� 5v      �� 5w      " ��6&   .R�5x      �� $ 	btnBorder��5y      #��z��5z      $	 8��5{      $

 True��5|      ._�5}      �� #��z��5~      #��z��5      5�      5�      5�       5�      P� 5�       �@J�IsMouseOver$
 True��61   . �5�      ��  � �� �#345A6E63   �� 5�       � z� 5�      �� 5�      $
 1.05Q�6-   $
 1.05Q�6;   5�      5�      5�      5�      .��5�      �  � RowDefinitions 5�   
   �� 5�      $
��0.3* �6   �� 5�      $
��0.3* �6   �� 5�      $
��0.3* �6   �� 5�      $
��0.3* �6   �� 5�      $
��0.3* �6   �� 5�      $
��0.3* �6   .+�5�   
   �� -   5         ��Name$ 	btnVolver��5�      $��0��5�      $
��Left=�5�      $��50��5�      $��50��5�      $��
10,10,0,21q�5�      #���  5�      5�      �� 5�   
   $��0��5�      $��Center=�5�      $��/Resources/LogoColor.png��5�      $��Uniform=�5�      �� 5�   
   -   5         ��Name$ txtDni��5�      $��1��5�      $	��300��5�      $��40��5�      $��0,10,0,0q�5�      $��Center=�5�      $��Center=�5�      #��[ 5�      
 � Tag$ DNI(sin letra)��5�       �� 	MaxLength$ 8��5�      �� 5�   
   -   5        $ 	txtCodigo��5�      $��2��5�      $	��300��5�      $��40��5�      $��0,10,0,0q�5�      $��Center=�5�      $��Center=�5�      #��[ 5�      $ Clave de acceso��5�      $ 4��5�      �� 5�   
   -   5        $ 	txtNombre��5�      $��3��5�      $	��300��5�      $��40��5�      $��0,10,0,0q�5�      $��Center=�5�      $��Center=�5�      #��[ 5�      $ Nombre��5�      �� 5�   
   -   5        $ txtApellidos��5�      $��4��5�      $	��300��5�      $��40��5�      $��0,10,0,0q�5�      $��Center=�5�      $��Center=�5�      #��[ 5�      $ 	Apellidos��5�      �� 5�   
   -   5        $ btnRegistrarse��5�      $��5��5�      $	��300��5�      $��40��5�      $��Center=�5�      $��Center=�5�      $��18(�5�      #���  5�      $��Registrarse��5�      5�      5�      5�      