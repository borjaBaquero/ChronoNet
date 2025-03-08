using System.Configuration;
using System.Data.SqlServerCe;

namespace CL_CHRONO
{
    internal sealed class AppSettings : ApplicationSettingsBase
    {
        private static readonly AppSettings _defaultInstance = (AppSettings)ApplicationSettingsBase.Synchronized(new AppSettings());

        public static AppSettings Default
        {
            get { return _defaultInstance; }
        }

        [UserScopedSetting()]
        [DefaultSettingValue("")]
        public string LastUserDNI
        {
            get { return (string)this["LastUserDNI"]; }
            set { this["LastUserDNI"] = value; }
        }

        [UserScopedSetting()]
        [DefaultSettingValue("0")]
        public int LastUserCode
        {
            get { return (int)this["LastUserCode"]; }
            set { this["LastUserCode"] = value; }
        }
    }
}
