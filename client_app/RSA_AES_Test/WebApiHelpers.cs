using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Net;
using System.Text;

namespace RSA_AES_Test
{
    public static class WebApiHelpers
    {
        public static T Get<T>(string apiUrl, Dictionary<string, string> headers, string actionPath)
        {
            try
            {
                var postUrl = BuildServerApiUrl(apiUrl, actionPath);

                using (var wb = new WebClient())
                {
                    foreach (var kvp in headers ?? new Dictionary<string, string>())
                    {
                        wb.Headers.Add(kvp.Key, kvp.Value);
                    }

                    var response = wb.DownloadString(postUrl);
                    return (T)Convert.ChangeType(response, typeof(T));
                }
            }
            catch (Exception)
            {
                return default(T);
            }
        }

        public static T Post<T>(string apiUrl, Dictionary<string, string> parameters, Dictionary<string, string> headers, string actionPath)
        {
            try
            {
                var postUrl = BuildServerApiUrl(apiUrl, actionPath);

                using (var wb = new WebClient())
                {
                    foreach (var kvp in headers ?? new Dictionary<string, string>())
                    {
                        wb.Headers.Add(kvp.Key, kvp.Value);
                    }

                    var data = new NameValueCollection();

                    if (parameters != null)
                    {
                        foreach (var o in parameters)
                        {
                            if (o.Value == null)
                                continue;

                            data.Add(o.Key, o.Value);
                        }
                    }

                    var responseBytes = wb.UploadValues(postUrl, "POST", data);
                    var responseStr = Encoding.UTF8.GetString(responseBytes);
                    return (T)Convert.ChangeType(responseStr, typeof(T));
                }
            }
            catch (Exception)
            {
                return default(T);
            }
        }

        private static string BuildServerApiUrl(string apiUrl, string path)
        {
            return $"{apiUrl}{path}";
        }
    }
}
