using Newtonsoft.Json;

namespace RSA_AES_Test
{
    internal class WebApiResponse
    {
        /// <summary>
        /// Json raw response
        /// </summary>
        [JsonProperty("data")]
        public string Data { get; set; }

        /// <summary>
        /// Signature of json (data) prob
        /// </summary>
        [JsonProperty("signature")]
        public string Signature { get; set; }
    }
}
