using System;
using System.Collections.Generic;
using System.Dynamic;
using System.IO;
using Newtonsoft.Json;

namespace RSA_AES_Test
{
    internal static class AES_Keys_Exchanger
    {
        public static bool Init(bool reInitAesKeys=false)
        {
            if (!string.IsNullOrEmpty(Settings.Instance?.AesKeys?.Key) && !string.IsNullOrEmpty(Settings.Instance?.AesKeys?.Iv) && !reInitAesKeys)
                return true;

            var isSessionInitiated = File.Exists("crypto.lock");

            if (reInitAesKeys)
            {
                return ExchangeAesKeys();
            }
            else
            {
                if (isSessionInitiated)
                    return true;
            }

            return ExchangeAesKeys();
        }

        private static bool ExchangeAesKeys()
        {
            try
            {
                // Generate AES Key
                var aesKeys = CryptoUtils.GenerateAesKeys();

                // Build the HTTP request body
                var requestHeaders = BuildRequestParamsForAesKeysExchange(aesKeys.Item1, aesKeys.Item2);

                // Send the request to the server
                var responseStr = WebApiHelpers.Get<string>(Settings.Instance.ApiBaseServerUrl, requestHeaders, "key_exchange.php");
                if (string.IsNullOrEmpty(responseStr)) return false;

                // Decrypt the response
                responseStr = CryptoUtils.AES_decrypt(responseStr, aesKeys.Item1, aesKeys.Item2);

                // Deserialize the response into a JSON object
                var response = JsonConvert.DeserializeObject<WebApiResponse>(responseStr);
                if (string.IsNullOrWhiteSpace(response?.Data) || string.IsNullOrEmpty(response.Signature)) return false;

                // Verify the server signature
                Console.WriteLine("[*] Received Response from the server.\n");
                Console.WriteLine($"[*] Response Body: {response.Data}");
                Console.WriteLine($"[*] Signature: {response.Signature}\n");
                
                var signatureValid = CryptoUtils.RSA_Verify(response.Data, response.Signature, Settings.Instance.ServerPublicKey);
                if (!signatureValid)
                {
                    // Handle.. Failed to verify the data source
                    Console.WriteLine("[-] WARNING! Failed to verify the server signature!");
                    Settings.Instance.AesKeys = null;
                    return false;
                }

                Console.WriteLine(@"[+] Server Signature is Valid!");

                // Key exchange is complete! Store the AES keys to be used by our application
                var rootObj = JsonConvert.DeserializeObject<RootObject>(response.Data);
                Settings.Instance.AesKeys = new AES_Keys { Key = aesKeys.Item1, Iv = aesKeys.Item2 };
                Settings.Instance.ClientIdentifier = rootObj.ClientIdentifier;

                Console.WriteLine($@"[+] CLIENT IDENTIFIER: {rootObj.ClientIdentifier}");
                File.WriteAllText("crypto.lock", "1");
                return true;
            }
            catch (Exception)
            {
                Settings.Instance.AesKeys = null;
                return false;
            }
        }

        private static Dictionary<string, string> BuildRequestParamsForAesKeysExchange(string aesKey, string aesIv)
        {
            dynamic data = new ExpandoObject();
            data.key = aesKey;
            data.iv = aesIv;

            Console.WriteLine($"[*] AES KEY: {aesKey}\n[*] AES IV: {aesIv}");

            string json = JsonConvert.SerializeObject(data);

            var jsonEncrypted = CryptoUtils.RSA_Encrypt(json, Settings.Instance.ServerPublicKey);

            return new Dictionary<string, string>
            {
                {"query", jsonEncrypted},
            };
        }
    }
}
