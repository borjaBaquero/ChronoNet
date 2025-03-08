using System;
using System.Collections.Generic;
using System.Linq;
using System.Data.SqlServerCe;

using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Media;

namespace CL_CHRONO
{
        public static class TextBoxWatermarkBehavior
        {
            public static readonly DependencyProperty WatermarkProperty =
                DependencyProperty.RegisterAttached("Watermark", typeof(string), typeof(TextBoxWatermarkBehavior),
                    new PropertyMetadata(default(string), OnWatermarkChanged));

            public static string GetWatermark(DependencyObject obj)
            {
                return (string)obj.GetValue(WatermarkProperty);
            }

            public static void SetWatermark(DependencyObject obj, string value)
            {
                obj.SetValue(WatermarkProperty, value);
            }

            private static void OnWatermarkChanged(DependencyObject d, DependencyPropertyChangedEventArgs e)
            {
                if (d is TextBox textBox)
                {
                    textBox.GotFocus -= RemoveWatermark;
                    textBox.LostFocus -= ShowWatermark;

                    if (!string.IsNullOrEmpty((string)e.NewValue))
                    {
                        textBox.GotFocus += RemoveWatermark;
                        textBox.LostFocus += ShowWatermark;
                        ShowWatermark(textBox, null);
                    }
                }
            }

            private static void RemoveWatermark(object sender, RoutedEventArgs e)
            {
                if (sender is TextBox textBox && textBox.Foreground == Brushes.Gray)
                {
                    textBox.Text = string.Empty;
                    textBox.Foreground = Brushes.Black;
                }
            }

            private static void ShowWatermark(object sender, RoutedEventArgs e)
            {
                if (sender is TextBox textBox && string.IsNullOrEmpty(textBox.Text))
                {
                    textBox.Foreground = Brushes.Gray;
                    textBox.Text = GetWatermark(textBox);
                }
            }
        }
    }
