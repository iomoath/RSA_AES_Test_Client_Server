using Newtonsoft.Json;

namespace RSA_AES_Test
{
    [JsonObject(MemberSerialization.OptOut)]
    public class RootObject
    {
        [JsonProperty("clientId")]
        public string ClientIdentifier { get; set; }

        /// <summary>
        /// Optional message added by web api
        /// </summary>
        [JsonProperty("message")]
        public string Message { get; set; }

        /// <summary>
        /// API Response Code
        /// </summary>
        [JsonProperty("result")]
        public int Result { get; set; }
    }
}
