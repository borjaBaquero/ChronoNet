﻿#pragma checksum "..\..\..\..\..\Vista\Inicio.xaml" "{ff1816ec-aa5e-4d10-87f7-6f4963833460}" "AEFC85BEE9FCDE71C1577FF3C3F520B9FD4F5071"
//------------------------------------------------------------------------------
// <auto-generated>
//     Este código fue generado por una herramienta.
//     Versión de runtime:4.0.30319.42000
//
//     Los cambios en este archivo podrían causar un comportamiento incorrecto y se perderán si
//     se vuelve a generar el código.
// </auto-generated>
//------------------------------------------------------------------------------

using CL_CHRONO;
using System;
using System.Diagnostics;
using System.Windows;
using System.Windows.Automation;
using System.Windows.Controls;
using System.Windows.Controls.Primitives;
using System.Windows.Controls.Ribbon;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Forms.Integration;
using System.Windows.Ink;
using System.Windows.Input;
using System.Windows.Markup;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Media.Effects;
using System.Windows.Media.Imaging;
using System.Windows.Media.Media3D;
using System.Windows.Media.TextFormatting;
using System.Windows.Navigation;
using System.Windows.Shapes;
using System.Windows.Shell;


namespace CL_CHRONO.Vista {
    
    
    /// <summary>
    /// Inicio
    /// </summary>
    public partial class Inicio : System.Windows.Window, System.Windows.Markup.IComponentConnector {
        
        
        #line 128 "..\..\..\..\..\Vista\Inicio.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.TextBox tbDNI;
        
        #line default
        #line hidden
        
        
        #line 144 "..\..\..\..\..\Vista\Inicio.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.TextBox tbCodigo;
        
        #line default
        #line hidden
        
        
        #line 167 "..\..\..\..\..\Vista\Inicio.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.Button btnAceptar;
        
        #line default
        #line hidden
        
        
        #line 180 "..\..\..\..\..\Vista\Inicio.xaml"
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1823:AvoidUnusedPrivateFields")]
        internal System.Windows.Controls.TextBlock txtDireccionIP;
        
        #line default
        #line hidden
        
        private bool _contentLoaded;
        
        /// <summary>
        /// InitializeComponent
        /// </summary>
        [System.Diagnostics.DebuggerNonUserCodeAttribute()]
        [System.CodeDom.Compiler.GeneratedCodeAttribute("PresentationBuildTasks", "8.0.1.0")]
        public void InitializeComponent() {
            if (_contentLoaded) {
                return;
            }
            _contentLoaded = true;
            System.Uri resourceLocater = new System.Uri("/CL_CHRONO_USERS;component/vista/inicio.xaml", System.UriKind.Relative);
            
            #line 1 "..\..\..\..\..\Vista\Inicio.xaml"
            System.Windows.Application.LoadComponent(this, resourceLocater);
            
            #line default
            #line hidden
        }
        
        [System.Diagnostics.DebuggerNonUserCodeAttribute()]
        [System.CodeDom.Compiler.GeneratedCodeAttribute("PresentationBuildTasks", "8.0.1.0")]
        [System.ComponentModel.EditorBrowsableAttribute(System.ComponentModel.EditorBrowsableState.Never)]
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Design", "CA1033:InterfaceMethodsShouldBeCallableByChildTypes")]
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Maintainability", "CA1502:AvoidExcessiveComplexity")]
        [System.Diagnostics.CodeAnalysis.SuppressMessageAttribute("Microsoft.Performance", "CA1800:DoNotCastUnnecessarily")]
        void System.Windows.Markup.IComponentConnector.Connect(int connectionId, object target) {
            switch (connectionId)
            {
            case 1:
            
            #line 12 "..\..\..\..\..\Vista\Inicio.xaml"
            ((CL_CHRONO.Vista.Inicio)(target)).Loaded += new System.Windows.RoutedEventHandler(this.Window_Loaded);
            
            #line default
            #line hidden
            return;
            case 2:
            this.tbDNI = ((System.Windows.Controls.TextBox)(target));
            
            #line 136 "..\..\..\..\..\Vista\Inicio.xaml"
            this.tbDNI.GotFocus += new System.Windows.RoutedEventHandler(this.Focus_tbCodigo);
            
            #line default
            #line hidden
            
            #line 137 "..\..\..\..\..\Vista\Inicio.xaml"
            this.tbDNI.PreviewTextInput += new System.Windows.Input.TextCompositionEventHandler(this.ValidateNumberInput);
            
            #line default
            #line hidden
            return;
            case 3:
            this.tbCodigo = ((System.Windows.Controls.TextBox)(target));
            
            #line 152 "..\..\..\..\..\Vista\Inicio.xaml"
            this.tbCodigo.GotFocus += new System.Windows.RoutedEventHandler(this.Focus_tbCodigo);
            
            #line default
            #line hidden
            
            #line 153 "..\..\..\..\..\Vista\Inicio.xaml"
            this.tbCodigo.PreviewTextInput += new System.Windows.Input.TextCompositionEventHandler(this.ValidateNumberInput);
            
            #line default
            #line hidden
            return;
            case 4:
            
            #line 164 "..\..\..\..\..\Vista\Inicio.xaml"
            ((System.Windows.Documents.Hyperlink)(target)).Click += new System.Windows.RoutedEventHandler(this.Hyperlink_Click);
            
            #line default
            #line hidden
            return;
            case 5:
            this.btnAceptar = ((System.Windows.Controls.Button)(target));
            
            #line 174 "..\..\..\..\..\Vista\Inicio.xaml"
            this.btnAceptar.Click += new System.Windows.RoutedEventHandler(this.Click_btnAceptar);
            
            #line default
            #line hidden
            return;
            case 6:
            this.txtDireccionIP = ((System.Windows.Controls.TextBlock)(target));
            return;
            }
            this._contentLoaded = true;
        }
    }
}

