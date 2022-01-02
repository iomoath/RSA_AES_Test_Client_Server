using System;

namespace RSA_AES_Test
{
    internal class Settings
    {
        private static Lazy<Settings> _lazyInstance = new Lazy<Settings>(() => new Settings());

        public static Settings Instance
        {
            get
            {
                if (_lazyInstance?.Value == null)
                    _lazyInstance = new Lazy<Settings>(() => new Settings());

                return _lazyInstance.Value;
            }
        }

        public AES_Keys AesKeys { get; set; }

        public string ServerPublicKey => "<RSAKeyValue>  <Modulus>hC1+a5mxexhm4Wz9Amc8RN5Luf6TK6HrWHKtOKGhkmNBATkyV6Hv9JswWcMzJ42OqFA5WFY9PikNPlQ2pv/AtDqcYkw2hIpiBhMZM+pl+XlSJuDZqr5Bf4LNTOuxTyDrU7r7H+2p2dnTwKL/uWG9yd3qqHYxqTmYxh7wcPwtnOA/JeBvxGYJOD2w7ov9PyzRPu7G+cDrMiCqzwOw2/EjSXWIOLY2ohj9ZOwXMvF2SlT+rIGtlet1k8yr4ZSw6ymozz9RoUeEXt8ndsCZzurJ6zwawuNNfB3VYl00hP9OALG9qmuLBds+RkwEpMryUGMYRFLJKBebRMGPguJPzNRTzw==</Modulus>  <Exponent>AQAB</Exponent></RSAKeyValue>";

        public string ApiBaseServerUrl => "http://10.9.8.10/bank_app/";

        /// <summary>
        /// This is provided by the server upon key exchange, should be used in all requests so the server can recognize us next time
        /// </summary>
        public string ClientIdentifier { get; set; }
    }
}
