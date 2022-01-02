using Newtonsoft.Json;

namespace RSA_AES_Test
{
    [JsonObject(MemberSerialization.OptIn)]

    internal class AES_Keys
    {
        [JsonProperty("key")]
        public string Key { get; set; }

        [JsonProperty("iv")]
        public string Iv { get; set; }
    }
}
