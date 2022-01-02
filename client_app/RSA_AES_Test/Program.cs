using System;
using System.Collections.Generic;
using System.IO;
using Newtonsoft.Json;

namespace RSA_AES_Test
{
    internal class Program
    {
        private static void Main()
        {
            // Generate AES key and exchange it with the remote server
            Console.WriteLine($@"[*] Attempting to establish a secure channel with: '{Settings.Instance.ApiBaseServerUrl}'...");
            var exchanged = AES_Keys_Exchanger.Init();

            if (exchanged)
            {
                Console.WriteLine(@"[+] Session key exchange is complete!");
                Console.WriteLine(@"[+] Channel is ready!");
            }
            else
            {
                Console.WriteLine("[-] Failed to exchange the session key! Aborting..");
            }

            Console.WriteLine();
            Console.WriteLine(@"[+] Test. Sending 'HELLO!' to the server..");
            var response = SendTestMessage("HELLO!");

            Console.WriteLine($"Response: {response.Message}");

            Console.Read();
        }

        private static RootObject SendTestMessage(string msg)
        {
            var clientId = CryptoUtils.RSA_Encrypt(Settings.Instance.ClientIdentifier, Settings.Instance.ServerPublicKey);
            var message = CryptoUtils.AES_encrypt(msg, Settings.Instance.AesKeys.Key, Settings.Instance.AesKeys.Iv);

            var headers = new Dictionary<string, string>
            {
                {"clientId", clientId},
            };
            var bodyParams = new Dictionary<string, string> { { "data", message } };

            var responseStr = WebApiHelpers.Post<string>(Settings.Instance.ApiBaseServerUrl, bodyParams, headers, "api.php");

            // Decrypt the response
            responseStr = CryptoUtils.AES_decrypt(responseStr, Settings.Instance.AesKeys.Key, Settings.Instance.AesKeys.Iv);

            // Deserialize the response into a JSON object
            var response = JsonConvert.DeserializeObject<WebApiResponse>(responseStr);
            if (string.IsNullOrWhiteSpace(response?.Data) || string.IsNullOrEmpty(response.Signature)) return null;

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
                return null;
            }

            Console.WriteLine(@"[+] Server Signature is Valid!");

            // Key exchange is complete! Store the AES keys to be used by our application
            return  JsonConvert.DeserializeObject<RootObject>(response.Data);

        }


    }
}
